<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class HouseNumber extends LocaleBasedRule implements Rule
{
    protected $countryRegexes = [
        'nl' => '/^([1-9]{1}((\s?-\s?[A-Za-z0-9]+)|[A-Za-z0-9 ]*))$/',
    ];

    /**
     * Determine if the validation rule passes.
     *
     * @param mixed  $value
     */
    public function passes(string $attribute, $value): bool
    {
        if (empty($this->country)) {
            return false;
        }
        if (! isset($this->countryRegexes[$this->country])) {
            return false;
        }

        // Move out the dashes. This is actually valid but can cause strange effects
        $value = str_replace(' ', '', trim($value));

        return false != preg_match($this->countryRegexes[$this->country], $value);
    }

    /**
     * Get the validation error message.
     */
    public function message(): string
    {
        return trans('validation.house_number');
    }
}
