<?php

namespace App\Http\Controllers\Customer\Auth;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Rules\KsaAddress;
use App\Rules\KsaPhoneNumber;
use App\Rules\KsaPostalCode;
use App\Services\OtpService;
use App\Services\VirtualAddressService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    protected $otpService;
    protected $virtualAddressService;

    public function __construct(OtpService $otpService, VirtualAddressService $virtualAddressService)
    {
        $this->otpService = $otpService;
        $this->virtualAddressService = $virtualAddressService;
    }

    /**
     * Show the registration form.
     */
    public function showRegistrationForm()
    {
        $pageTitle = 'Customer Registration';
        return view('customer.auth.register', compact('pageTitle'));
    }

    /**
     * Handle registration - Send OTP for verification.
     */
    public function register(Request $request)
    {
        // Verify CAPTCHA
        if (!verifyCaptcha()) {
            $notify[] = ['error', 'Invalid captcha provided'];
            return back()->withNotify($notify)->withInput();
        }

        $request->validate([
            'firstname' => 'required|string|max:40',
            'lastname' => 'required|string|max:40',
            'email' => 'required|email|max:255|unique:customers,email',
            'password' => 'required|string|min:6|confirmed',
            'mobile' => ['required', new KsaPhoneNumber(), 'unique:customers,mobile'],
            'country_code' => 'required|string|max:10',
            'address' => ['required', 'string', new KsaAddress()],
            'city' => 'required|string|max:40',
            'state' => 'required|string|max:40',
            'postal_code' => ['required', new KsaPostalCode()],
        ]);

        // Normalize phone number
        $mobile = \App\Rules\KsaPhoneNumber::normalize($request->mobile);

        // Check if mobile already exists
        if (Customer::where('mobile', $mobile)->exists()) {
            return back()->with('error', 'Mobile number already registered.');
        }

        // Store registration data in session (including password hash)
        $registrationData = [
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'mobile' => $mobile,
            'country_code' => $request->country_code,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'postal_code' => $request->postal_code,
            'otp_method' => 'email', // Use email for OTP
        ];

        session(['registration_data' => $registrationData]);

        // Send OTP via email
        $this->otpService->send(null, $request->email, 'email', 'registration');

        return redirect()->route('customer.register.verify')->with('success', 'OTP has been sent to your email. Please verify to complete registration.');
    }

    /**
     * Show OTP verification form.
     */
    public function showVerifyForm()
    {
        if (!session()->has('registration_data')) {
            $notify[] = ['error', 'Registration session expired. Please start again.'];
            return redirect()->route('customer.register')->withNotify($notify);
        }

        $pageTitle = 'Verify OTP';
        $registrationData = session('registration_data');

        // Get current OTP log to show attempts
        $contact = $registrationData['otp_method'] === 'email'
            ? $registrationData['email']
            : $registrationData['mobile'];

        $otpLog = \App\Models\OtpLog::recentFor($contact, 'registration')->first();

        return view('customer.auth.verify_otp', compact('pageTitle', 'registrationData', 'otpLog'));
    }

    /**
     * Handle OTP verification and complete registration.
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp_code' => 'required|string|size:6',
        ], [
            'otp_code.required' => 'Please enter the OTP code.',
            'otp_code.size' => 'OTP code must be exactly 6 digits.',
        ]);

        if (!session()->has('registration_data')) {
            $notify[] = ['error', 'Registration session expired. Please start again.'];
            return redirect()->route('customer.register')->withNotify($notify);
        }

        $registrationData = session('registration_data');

        // Determine contact based on OTP method
        $contact = $registrationData['otp_method'] === 'email'
            ? $registrationData['email']
            : $registrationData['mobile'];

        // Check if there's a pending OTP
        $pendingOtp = \App\Models\OtpLog::recentFor($contact, 'registration')->first();

        if (!$pendingOtp) {
            $notify[] = ['error', 'No active OTP found. Please request a new one.'];
            return redirect()->route('customer.register.verify')->withNotify($notify);
        }

        // Check if OTP has expired
        if ($pendingOtp->isExpired()) {
            $pendingOtp->markAsExpired();
            $notify[] = ['error', 'OTP has expired. Please request a new one.'];
            return redirect()->route('customer.register.verify')->withNotify($notify);
        }

        // Check if max attempts reached
        if ($pendingOtp->maxAttemptsReached()) {
            $notify[] = ['error', 'Maximum verification attempts reached. Please request a new OTP.'];
            return redirect()->route('customer.register.verify')->withNotify($notify);
        }

        // Verify OTP
        $otpLog = $this->otpService->verify($contact, $request->otp_code, 'registration');

        if (!$otpLog) {
            // Refresh the OTP log to get updated attempts count
            $pendingOtp->refresh();
            $remainingAttempts = 3 - $pendingOtp->attempts;
            $notify[] = ['error', "Invalid OTP code. You have {$remainingAttempts} attempt(s) remaining."];
            return redirect()->route('customer.register.verify')->withNotify($notify);
        }

        // Create customer account
        $customer = Customer::create([
            'firstname' => $registrationData['firstname'],
            'lastname' => $registrationData['lastname'],
            'email' => $registrationData['email'],
            'password' => $registrationData['password'], // Already hashed
            'mobile' => $registrationData['mobile'],
            'country_code' => $registrationData['country_code'],
            'address' => $registrationData['address'],
            'city' => $registrationData['city'],
            'state' => $registrationData['state'],
            'postal_code' => $registrationData['postal_code'],
            'status' => 1, // Active
            'email_verified_at' => $registrationData['otp_method'] === 'email' ? now() : null,
            'mobile_verified_at' => null,
        ]);

        // Assign virtual address
        $this->virtualAddressService->assignToCustomer($customer);

        // Log the customer in
        Auth::guard('customer')->login($customer);

        // Update last login
        $customer->updateLastLogin();

        // Clear registration session data
        session()->forget('registration_data');

        return redirect()->route('customer.dashboard')->with('success', 'Registration successful! Welcome to ' . gs('site_name'));
    }

    /**
     * Resend OTP.
     */
    public function resendOtp(Request $request)
    {
        if (!session()->has('registration_data')) {
            $notify[] = ['error', 'Registration session expired. Please start again.'];
            return redirect()->route('customer.register')->withNotify($notify);
        }

        $registrationData = session('registration_data');

        // Determine contact based on OTP method
        $contact = $registrationData['otp_method'] === 'email'
            ? $registrationData['email']
            : $registrationData['mobile'];

        // Rate limiting: Check if last OTP was sent less than 30 seconds ago
        $lastOtp = \App\Models\OtpLog::where(function ($q) use ($contact) {
            $q->where('email', $contact)
              ->orWhere('mobile', $contact);
        })
        ->where('purpose', 'registration')
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
                null,
                $contact,
                $registrationData['otp_method'],
                'registration'
            );

            $notify[] = ['success', 'A new OTP has been sent to your ' . $registrationData['otp_method'] . '. Please check and enter the code.'];
            return back()->withNotify($notify);

        } catch (\Exception $e) {
            $notify[] = ['error', 'Failed to send OTP. Please try again later.'];
            return back()->withNotify($notify);
        }
    }
}
