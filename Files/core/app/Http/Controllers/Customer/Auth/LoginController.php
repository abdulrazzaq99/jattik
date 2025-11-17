<?php

namespace App\Http\Controllers\Customer\Auth;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerLoginLog;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    protected $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        $pageTitle = 'Customer Login';
        return view('customer.auth.login', compact('pageTitle'));
    }

    /**
     * Handle login request - Verify password then send OTP.
     */
    public function login(Request $request)
    {
        $request->validate([
            'contact' => 'required|string', // Can be email or mobile
            'password' => 'required|string',
        ], [
            'contact.required' => 'Please enter your email or mobile number.',
            'password.required' => 'Please enter your password.',
        ]);

        // Find customer by email or mobile
        $customer = Customer::where('email', $request->contact)
            ->orWhere('mobile', $request->contact)
            ->first();

        if (!$customer) {
            return back()
                ->withInput($request->only('contact'))
                ->with('error', 'No account found with this email or mobile number. Please check and try again.');
        }

        // Check if customer is active
        if (!$customer->isActive()) {
            return back()
                ->withInput($request->only('contact'))
                ->with('error', 'Your account is inactive. Please contact support for assistance.');
        }

        // Check password - if password is null, allow login (for customers created by staff)
        if ($customer->password && !\Hash::check($request->password, $customer->password)) {
            return back()
                ->withInput($request->only('contact'))
                ->with('error', 'Incorrect password. Please try again or use the forgot password option.');
        }

        // If password is null (old customers), set the password now
        if (!$customer->password) {
            $customer->password = \Hash::make($request->password);
            $customer->save();
        }

        // TEMPORARY: OTP verification disabled - direct login
        // Store customer ID and OTP method in session
        // $otpMethod = 'email'; // Default to email
        // session([
        //     'login_customer_id' => $customer->id,
        //     'login_otp_method' => $otpMethod,
        // ]);

        // Send OTP via email
        // $this->otpService->send($customer, $customer->email, $otpMethod, 'login');

        // return redirect()->route('customer.login.verify')->with('success', 'OTP has been sent to your email. Please verify to complete login.');

        // Direct login without OTP
        Auth::guard('customer')->login($customer);

        // Update last login
        $customer->updateLastLogin();

        // Create login log
        CustomerLoginLog::create([
            'customer_id' => $customer->id,
            'login_at' => now(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'login_method' => 'password_only',
            'session_id' => session()->getId(),
        ]);

        return redirect()->route('customer.dashboard')->with('success', 'Welcome back, ' . $customer->fullname . '!');
    }

    /**
     * Show OTP verification form for login.
     */
    public function showVerifyForm()
    {
        if (!session()->has('login_customer_id')) {
            $notify[] = ['error', 'Login session expired. Please start again.'];
            return redirect()->route('customer.login')->withNotify($notify);
        }

        $pageTitle = 'Verify Login OTP';
        $customer = Customer::find(session('login_customer_id'));
        $otpMethod = session('login_otp_method');

        // Get current OTP log to show attempts
        $contact = $otpMethod === 'email' ? $customer->email : $customer->mobile;
        $otpLog = \App\Models\OtpLog::recentFor($contact, 'login')->first();

        return view('customer.auth.verify_login_otp', compact('pageTitle', 'customer', 'otpMethod', 'otpLog'));
    }

    /**
     * Handle OTP verification and complete login.
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp_code' => 'required|string|size:6',
        ], [
            'otp_code.required' => 'Please enter the OTP code.',
            'otp_code.size' => 'OTP code must be exactly 6 digits.',
        ]);

        if (!session()->has('login_customer_id')) {
            $notify[] = ['error', 'Login session expired. Please start again.'];
            return redirect()->route('customer.login')->withNotify($notify);
        }

        $customer = Customer::find(session('login_customer_id'));

        if (!$customer) {
            $notify[] = ['error', 'Customer not found.'];
            return redirect()->route('customer.login')->withNotify($notify);
        }

        // Determine contact based on OTP method
        $otpMethod = session('login_otp_method');
        $contact = $otpMethod === 'email' ? $customer->email : $customer->mobile;

        // Check if there's a pending OTP
        $pendingOtp = \App\Models\OtpLog::recentFor($contact, 'login')->first();

        if (!$pendingOtp) {
            $notify[] = ['error', 'No active OTP found. Please request a new one.'];
            return redirect()->route('customer.login.verify')->withNotify($notify);
        }

        // Check if OTP has expired
        if ($pendingOtp->isExpired()) {
            $pendingOtp->markAsExpired();
            $notify[] = ['error', 'OTP has expired. Please request a new one.'];
            return redirect()->route('customer.login.verify')->withNotify($notify);
        }

        // Check if max attempts reached
        if ($pendingOtp->maxAttemptsReached()) {
            $notify[] = ['error', 'Maximum verification attempts reached. Please request a new OTP.'];
            return redirect()->route('customer.login.verify')->withNotify($notify);
        }

        // Verify OTP
        $otpLog = $this->otpService->verify($contact, $request->otp_code, 'login');

        if (!$otpLog) {
            // Refresh the OTP log to get updated attempts count
            $pendingOtp->refresh();
            $remainingAttempts = 3 - $pendingOtp->attempts;
            $notify[] = ['error', "Invalid OTP code. You have {$remainingAttempts} attempt(s) remaining."];
            return redirect()->route('customer.login.verify')->withNotify($notify);
        }

        // Log the customer in
        Auth::guard('customer')->login($customer);

        // Update last login
        $customer->updateLastLogin();

        // Create login log
        CustomerLoginLog::create([
            'customer_id' => $customer->id,
            'login_at' => now(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'login_method' => 'otp_' . $otpMethod,
            'session_id' => session()->getId(),
        ]);

        // Clear login session data
        session()->forget(['login_customer_id', 'login_otp_method']);

        return redirect()->route('customer.dashboard')->with('success', 'Welcome back, ' . $customer->fullname . '!');
    }

    /**
     * Resend OTP for login.
     */
    public function resendOtp(Request $request)
    {
        if (!session()->has('login_customer_id')) {
            $notify[] = ['error', 'Login session expired. Please start again.'];
            return redirect()->route('customer.login')->withNotify($notify);
        }

        $customer = Customer::find(session('login_customer_id'));
        $otpMethod = session('login_otp_method');

        if (!$customer) {
            $notify[] = ['error', 'Customer not found.'];
            return redirect()->route('customer.login')->withNotify($notify);
        }

        // Determine contact based on OTP method
        $contact = $otpMethod === 'email' ? $customer->email : $customer->mobile;

        // Rate limiting: Check if last OTP was sent less than 30 seconds ago
        $lastOtp = \App\Models\OtpLog::where(function ($q) use ($contact) {
            $q->where('email', $contact)
              ->orWhere('mobile', $contact);
        })
        ->where('purpose', 'login')
        ->latest('sent_at')
        ->first();

        if ($lastOtp && $lastOtp->sent_at->diffInSeconds(now()) < 30) {
            $remainingSeconds = ceil(30 - $lastOtp->sent_at->diffInSeconds(now()));
            $notify[] = ['error', "Please wait {$remainingSeconds} seconds before requesting another OTP."];
            return back()->withNotify($notify);
        }

        try {
            // Resend OTP
            $this->otpService->resend(
                $customer,
                $contact,
                $otpMethod,
                'login'
            );

            $notify[] = ['success', 'A new OTP has been sent to your ' . $otpMethod . '. Please check and enter the code.'];
            return back()->withNotify($notify);

        } catch (\Exception $e) {
            $notify[] = ['error', 'Failed to send OTP. Please try again later.'];
            return back()->withNotify($notify);
        }
    }

    /**
     * Log the customer out.
     */
    public function logout(Request $request)
    {
        $customer = Auth::guard('customer')->user();

        if ($customer) {
            // Update login log with logout time
            $loginLog = CustomerLoginLog::where('customer_id', $customer->id)
                ->where('session_id', session()->getId())
                ->whereNull('logout_at')
                ->first();

            if ($loginLog) {
                $loginLog->recordLogout();
            }
        }

        Auth::guard('customer')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('customer.login')->with('success', 'You have been logged out successfully.');
    }
}
