<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class EmailOrPhoneNumber implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // 1. Try to validate as an email
        if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return; // It's a valid email, validation passed
        }

        // 2. Try to validate as a simple phone number (string of digits, max 10)
        // Check if the value is purely numeric and its length is not more than 10
        if (ctype_digit($value) && strlen($value) <= 10) {
            return; // It's a valid simple phone number, validation passed
        }

        // If it's neither a valid email nor a valid simple phone number, validation fails.
        $fail('The :attribute must be a valid email address or a phone number (up to 10 digits).');
    }
}
