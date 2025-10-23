<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidateCpf implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Remove any non-numeric characters
        $cpf = preg_replace('/[^0-9]/', '', $value);

        // Check if CPF has 11 digits
        if (strlen($cpf) != 11) {
            $fail('The :attribute must have 11 digits.');
            return;
        }

        // Check for repeated numbers
        if (preg_match('/^(\d)\1*$/', $cpf)) {
            $fail('The :attribute is invalid.');
            return;
        }

        // Calculate first verification digit
        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += $cpf[$i] * (10 - $i);
        }
        $remainder = $sum % 11;
        $digit1 = $remainder < 2 ? 0 : 11 - $remainder;

        // Check first verification digit
        if ($cpf[9] != $digit1) {
            $fail('The :attribute is invalid.');
            return;
        }

        // Calculate second verification digit
        $sum = 0;
        for ($i = 0; $i < 10; $i++) {
            $sum += $cpf[$i] * (11 - $i);
        }
        $remainder = $sum % 11;
        $digit2 = $remainder < 2 ? 0 : 11 - $remainder;

        // Check second verification digit
        if ($cpf[10] != $digit2) {
            $fail('The :attribute is invalid.');
            return;
        }
    }
}
