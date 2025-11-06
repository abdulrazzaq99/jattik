<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\OtpLog;
use Illuminate\Support\Facades\Request;

class OtpService
{
    /**
     * OTP validity duration in minutes.
     */
    const OTP_VALIDITY_MINUTES = 10;

    /**
     * Generate a random OTP code.
     *
     * @param  int  $length
     * @return string
     */
    public function generateCode(int $length = 6): string
    {
        return str_pad((string) random_int(0, 999999), $length, '0', STR_PAD_LEFT);
    }

    /**
     * Send OTP to customer via email or SMS.
     *
     * @param  Customer|null  $customer
     * @param  string  $contact (email or mobile)
     * @param  string  $type ('email', 'sms', 'whatsapp')
     * @param  string  $purpose ('registration', 'login', 'password_reset')
     * @return OtpLog
     */
    public function send($customer, string $contact, string $type, string $purpose): OtpLog
    {
        // Generate OTP code
        $code = $this->generateCode();

        // Calculate expiry time
        $expiresAt = now()->addMinutes(self::OTP_VALIDITY_MINUTES);

        // Create OTP log
        $otpLog = OtpLog::create([
            'customer_id' => $customer ? $customer->id : null,
            'email' => $type === 'email' ? $contact : null,
            'mobile' => in_array($type, ['sms', 'whatsapp']) ? $contact : null,
            'otp_code' => $code,
            'otp_type' => $type,
            'purpose' => $purpose,
            'sent_at' => now(),
            'expires_at' => $expiresAt,
            'status' => 'pending',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        // Determine template name
        $templateName = match($purpose) {
            'registration' => 'CUSTOMER_OTP_REGISTRATION',
            'login' => 'CUSTOMER_OTP_LOGIN',
            'password_reset' => 'CUSTOMER_OTP_PASSWORD_RESET',
            default => 'CUSTOMER_OTP_LOGIN',
        };

        // Prepare shortcodes
        $shortcodes = [
            'fullname' => $customer ? $customer->fullname : 'Customer',
            'otp_code' => $code,
            'otp_validity' => self::OTP_VALIDITY_MINUTES,
            'ip_address' => request()->ip(),
            'login_time' => now()->format('Y-m-d H:i:s'),
        ];

        // Create temporary user object if no customer exists yet
        $userObject = $customer ?? (object)[
            'email' => $type === 'email' ? $contact : null,
            'mobile' => in_array($type, ['sms', 'whatsapp']) ? $contact : null,
            'fullname' => 'Customer',
        ];

        // Send notification
        notify($userObject, $templateName, $shortcodes, [$type]);

        return $otpLog;
    }

    /**
     * Verify OTP code.
     *
     * @param  string  $contact (email or mobile)
     * @param  string  $code
     * @param  string  $purpose
     * @return OtpLog|null
     */
    public function verify(string $contact, string $code, string $purpose): ?OtpLog
    {
        // Find recent OTP log
        $otpLog = OtpLog::recentFor($contact, $purpose)->first();

        if (!$otpLog) {
            return null;
        }

        // Check if expired
        if ($otpLog->isExpired()) {
            $otpLog->markAsExpired();
            return null;
        }

        // Check if max attempts reached
        if ($otpLog->maxAttemptsReached()) {
            return null;
        }

        // Verify code
        if ($otpLog->otp_code !== $code) {
            $otpLog->incrementAttempts();
            return null;
        }

        // Mark as verified
        $otpLog->markAsVerified();

        return $otpLog;
    }

    /**
     * Resend OTP (invalidate old one and send new).
     *
     * @param  Customer|null  $customer
     * @param  string  $contact
     * @param  string  $type
     * @param  string  $purpose
     * @return OtpLog
     */
    public function resend($customer, string $contact, string $type, string $purpose): OtpLog
    {
        // Invalidate all pending OTPs for this contact and purpose
        OtpLog::where(function ($q) use ($contact) {
            $q->where('email', $contact)
              ->orWhere('mobile', $contact);
        })
        ->where('purpose', $purpose)
        ->where('status', 'pending')
        ->update(['status' => 'expired']);

        // Send new OTP
        return $this->send($customer, $contact, $type, $purpose);
    }

    /**
     * Clean up expired OTPs (run daily).
     *
     * @return int Number of OTPs deleted
     */
    public function cleanupExpired(): int
    {
        return OtpLog::where('expires_at', '<', now())
            ->where('status', 'pending')
            ->update(['status' => 'expired']);
    }
}
