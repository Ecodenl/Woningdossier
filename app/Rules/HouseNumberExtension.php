<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class HouseNumberExtension extends LocaleBasedRule implements Rule
{
    protected $countryRegexes = [
        // This regex supports alphanumeric keys up to 5 characters (including spaces and forward slashes), and the word "beneden"
        'nl' => '/^([a-zA-Z0-9 \/]{0,5}|beneden)$/',
        'be' => '/^([a-zA-Z0-9 \/]{0,5}|beneden)$/', // TODO: Duplicate for now...
    ];

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
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
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.house_number_extension');
    }
}
