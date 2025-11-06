<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class KsaPostalCode implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // KSA postal codes are 5 digits
        // Format: XXXXX (e.g., 12345)

        if (!preg_match('/^[0-9]{5}$/', $value)) {
            $fail('The :attribute must be a valid KSA postal code (5 digits, e.g., 12345).');
        }
    }
}
