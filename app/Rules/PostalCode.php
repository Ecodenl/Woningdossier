<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Contracts\Validation\ValidationRule;

class PostalCode extends LocaleBasedRule implements ValidationRule
{
    protected $countryRegexes = [
        'nl' => '/^[1-9][0-9]{3} ?(?!sa|sd|ss)[a-z]{2}$/i',
        'be' => '/^[1-9][0-9]{3}$/i',
    ];

    /**
     * Determine if the validation rule passes.
     *
     * @param mixed  $value
     */
    public function passes($attribute, $value): bool
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

    public function validate(string $attribute, mixed $value, \Closure $fail): void
    {
        if (! $this->passes($attribute, $value)) {
            $fail($this->message());
        }
    }
}
