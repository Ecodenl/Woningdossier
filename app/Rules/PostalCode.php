<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class PostalCode extends LocaleBasedRule implements Rule
{
    protected $countryRegexes = [
        'nl' => '/^[1-9][0-9]{3} ?(?!sa|sd|ss)[a-z]{2}$/i',
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

        return false != preg_match($this->countryRegexes[$this->country], $value);
    }

    /**
     * Get the validation error message.
     */
    public function message(): string
    {
        return trans('validation.postal_code');
    }
}
