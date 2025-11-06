<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class KsaPhoneNumber implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Remove any spaces, dashes, or parentheses
        $cleanNumber = preg_replace('/[\s\-\(\)]/', '', $value);

        // KSA phone numbers validation:
        // - Mobile numbers start with 05 followed by 8 digits (total 10 digits)
        // - Can also be in international format: +9665XXXXXXXX or 009665XXXXXXXX

        $patterns = [
            '/^05[0-9]{8}$/',              // 05XXXXXXXX (local format)
            '/^\+9665[0-9]{8}$/',          // +9665XXXXXXXX (international format)
            '/^009665[0-9]{8}$/',          // 009665XXXXXXXX (international format with 00)
            '/^9665[0-9]{8}$/',            // 9665XXXXXXXX (without + prefix)
        ];

        $isValid = false;
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $cleanNumber)) {
                $isValid = true;
                break;
            }
        }

        if (!$isValid) {
            $fail('The :attribute must be a valid KSA mobile number (e.g., 05XXXXXXXX or +9665XXXXXXXX).');
        }
    }

    /**
     * Normalize KSA phone number to standard format (05XXXXXXXX).
     *
     * @param  string  $phoneNumber
     * @return string
     */
    public static function normalize(string $phoneNumber): string
    {
        // Remove any spaces, dashes, or parentheses
        $cleanNumber = preg_replace('/[\s\-\(\)]/', '', $phoneNumber);

        // Convert to local format (05XXXXXXXX)
        if (preg_match('/^\+9665([0-9]{8})$/', $cleanNumber, $matches)) {
            return '05' . $matches[1];
        } elseif (preg_match('/^009665([0-9]{8})$/', $cleanNumber, $matches)) {
            return '05' . $matches[1];
        } elseif (preg_match('/^9665([0-9]{8})$/', $cleanNumber, $matches)) {
            return '05' . $matches[1];
        }

        return $cleanNumber;
    }

    /**
     * Get international format (+966).
     *
     * @param  string  $phoneNumber
     * @return string
     */
    public static function toInternational(string $phoneNumber): string
    {
        $normalized = self::normalize($phoneNumber);

        // Convert 05XXXXXXXX to +9665XXXXXXXX
        if (preg_match('/^05([0-9]{8})$/', $normalized, $matches)) {
            return '+9665' . $matches[1];
        }

        return $phoneNumber;
    }
}
