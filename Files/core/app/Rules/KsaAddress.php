<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class KsaAddress implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // KSA address should contain at least city and some details
        // Minimum length check
        if (strlen($value) < 10) {
            $fail('The :attribute must contain a detailed address (at least 10 characters).');
            return;
        }

        // Check if address contains Arabic or English text
        $hasArabic = preg_match('/[\x{0600}-\x{06FF}]/u', $value);
        $hasEnglish = preg_match('/[a-zA-Z]/', $value);

        if (!$hasArabic && !$hasEnglish) {
            $fail('The :attribute must contain valid text (Arabic or English).');
        }
    }
}
