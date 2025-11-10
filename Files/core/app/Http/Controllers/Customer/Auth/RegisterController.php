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
            return redirect()->route('customer.register')->with('error', 'Registration session expired. Please start again.');
        }

        $pageTitle = 'Verify OTP';
        $registrationData = session('registration_data');
        return view('customer.auth.verify_otp', compact('pageTitle', 'registrationData'));
    }

    /**
     * Handle OTP verification and complete registration.
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp_code' => 'required|string|size:6',
        ]);

        if (!session()->has('registration_data')) {
            return back()->with('error', 'Registration session expired. Please start again.');
        }

        $registrationData = session('registration_data');

        // Determine contact based on OTP method
        $contact = $registrationData['otp_method'] === 'email'
            ? $registrationData['email']
            : $registrationData['mobile'];

        // Verify OTP
        $otpLog = $this->otpService->verify($contact, $request->otp_code, 'registration');

        if (!$otpLog) {
            return back()->with('error', 'Invalid or expired OTP. Please try again.');
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
            'mobile_verified_at' => $registrationData['otp_method'] === 'sms' ? now() : null,
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
            return back()->with('error', 'Registration session expired. Please start again.');
        }

        $registrationData = session('registration_data');

        // Determine contact based on OTP method
        $contact = $registrationData['otp_method'] === 'email'
            ? $registrationData['email']
            : $registrationData['mobile'];

        // Resend OTP
        $this->otpService->resend(
            null,
            $contact,
            $registrationData['otp_method'],
            'registration'
        );

        return back()->with('success', 'OTP resent successfully!');
    }
}
