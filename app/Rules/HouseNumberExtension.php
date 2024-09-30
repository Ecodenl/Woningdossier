<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class HouseNumberExtension extends LocaleBasedRule implements Rule
{
    protected $countryRegexes = [
        // This regex supports alphanumeric keys up to 5 characters (including spaces and forward slashes), and the word "beneden"
        'nl' => '/^([a-zA-Z0-9 \/]{0,5}|beneden)$/',
    ];

    /**
     * Determine if the validation rule passes.
     *
     * @param mixed $value
     */
    public function passes(string $attribute, $value): bool
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
}
