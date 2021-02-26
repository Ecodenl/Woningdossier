<?php

namespace App\Rules\Api\V1;

use App\Rules\LocaleBasedRule;
use Illuminate\Contracts\Validation\Rule;

class HouseNumberExtension extends LocaleBasedRule implements Rule
{
    protected $countryRegexes = [
        'nl' => '/^(boven|beneden|onder|hs|bis|zw|rd|[\w]{0,10}|[0-9]|[\da-z][\da-z]?)$/',
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
        $value = str_replace(' ', '', trim($value));

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
