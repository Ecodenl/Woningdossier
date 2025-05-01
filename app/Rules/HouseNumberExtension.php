<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Contracts\Validation\ValidationRule;

class HouseNumberExtension extends LocaleBasedRule implements ValidationRule
{
    protected $countryRegexes = [
        // This regex supports alphanumeric keys up to 5 characters (including spaces and forward slashes), and the word "beneden"
        'nl' => '/^([a-zA-Z0-9 \/]{0,5}|beneden)$/',
        'be' => '/^([a-zA-Z0-9 \/]{0,5}|beneden)$/', // TODO: Duplicate for now...
    ];

    /**
     * Determine if the validation rule passes.
     *
     * @param mixed $value
     */
    public function passes($attribute, $value): bool
    {
        if (empty($this->country)) {
            return false;
        }
        if (! isset($this->countryRegexes[$this->country])) {
            return false;
        }

        // Rule is dumb on purpose, entered value should be cleaned beforehand
        return false != preg_match($this->countryRegexes[$this->country], $value);
    }

    /**
     * Get the validation error message.
     */
    public function message(): string
    {
        return trans('validation.house_number_extension');
    }

    public function validate(string $attribute, mixed $value, \Closure $fail): void
    {
        if (! $this->passes($attribute, $value)) {
            $fail($this->message());
        }
    }
}
