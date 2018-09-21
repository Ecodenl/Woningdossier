<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class PhoneNumber extends LocaleBasedRule implements Rule
{
    protected $countryRegexes = [
        'nl' => '/(^\+[0-9]{2}|^\+[0-9]{2}\(0\)|^\(\+[0-9]{2}\)\(0\)|^00[0-9]{2}|^0)([0-9]{9}$|[0]{1}[0-9\-\s]{9}$)|^\(0[1-9]{2}\)\s?[0-9]{7}|^\(0[1-9]{3}\)\s?[0-9]{6}|(\+31[0]?[1-9]{1}[0-9\s\-]{9})/',
    ];

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed  $value
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

        // Move out the dashes. This is actually valid but can cause strange effects
        $value = str_replace('-', '', $value);
        $value = str_replace(' ', '', $value);

        return false != preg_match($this->countryRegexes[$this->country], $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.phone_number');
    }
}
