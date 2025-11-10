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
        ]);

        // Find customer by email or mobile
        $customer = Customer::where('email', $request->contact)
            ->orWhere('mobile', $request->contact)
            ->first();

        if (!$customer) {
            return back()->with('error', 'Invalid credentials.');
        }

        // Check if customer is active
        if (!$customer->isActive()) {
            return back()->with('error', 'Your account is inactive. Please contact support.');
        }

        // Check password - if password is null, allow login (for customers created by staff)
        if ($customer->password && !\Hash::check($request->password, $customer->password)) {
            return back()->with('error', 'Invalid credentials.');
        }

        // If password is null (old customers), set the password now
        if (!$customer->password) {
            $customer->password = \Hash::make($request->password);
            $customer->save();
        }

        // Store customer ID and OTP method in session
        $otpMethod = 'email'; // Default to email
        session([
            'login_customer_id' => $customer->id,
            'login_otp_method' => $otpMethod,
        ]);

        // Send OTP via email
        $this->otpService->send($customer, $customer->email, $otpMethod, 'login');

        return redirect()->route('customer.login.verify')->with('success', 'OTP has been sent to your email. Please verify to complete login.');
    }

    /**
     * Show OTP verification form for login.
     */
    public function showVerifyForm()
    {
        if (!session()->has('login_customer_id')) {
            return redirect()->route('customer.login')->with('error', 'Login session expired. Please start again.');
        }

        $pageTitle = 'Verify Login OTP';
        $customer = Customer::find(session('login_customer_id'));
        $otpMethod = session('login_otp_method');

        return view('customer.auth.verify_login_otp', compact('pageTitle', 'customer', 'otpMethod'));
    }

    /**
     * Handle OTP verification and complete login.
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp_code' => 'required|string|size:6',
        ]);

        if (!session()->has('login_customer_id')) {
            return back()->with('error', 'Login session expired. Please start again.');
        }

        $customer = Customer::find(session('login_customer_id'));

        if (!$customer) {
            return back()->with('error', 'Customer not found.');
        }

        // Determine contact based on OTP method
        $otpMethod = session('login_otp_method');
        $contact = $otpMethod === 'email' ? $customer->email : $customer->mobile;

        // Verify OTP
        $otpLog = $this->otpService->verify($contact, $request->otp_code, 'login');

        if (!$otpLog) {
            return back()->with('error', 'Invalid or expired OTP. Please try again.');
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
            return back()->with('error', 'Login session expired. Please start again.');
        }

        $customer = Customer::find(session('login_customer_id'));
        $otpMethod = session('login_otp_method');

        if (!$customer) {
            return back()->with('error', 'Customer not found.');
        }

        // Determine contact based on OTP method
        $contact = $otpMethod === 'email' ? $customer->email : $customer->mobile;

        // Resend OTP
        $this->otpService->resend(
            $customer,
            $contact,
            $otpMethod,
            'login'
        );

        return back()->with('success', 'OTP resent successfully!');
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
