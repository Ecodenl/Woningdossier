<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class HouseNumber extends LocaleBasedRule implements ValidationRule
{
    protected $countryRegexes = [
        'nl' => '/^([1-9]{1}((\s?-\s?[A-Za-z0-9]+)|[A-Za-z0-9 ]*))$/',
        'be' => '/^([1-9]{1}((\s?-\s?[A-Za-z0-9]+)|[A-Za-z0-9 ]*))$/',
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

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $this->passes($attribute, $value)) {
            $fail($this->message());
        }
    }
}
