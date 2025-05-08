<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Contracts\Validation\ValidationRule;

class PhoneNumber extends LocaleBasedRule implements ValidationRule
{
    protected $countryRegexes = [
        'nl' => '/(^\+[0-9]{2}|^\+[0-9]{2}\(0\)|^\(\+[0-9]{2}\)\(0\)|^00[0-9]{2}|^0)([0-9]{9}$|[0]{1}[0-9\-\s]{9}$)|^\(0[1-9]{2}\)\s?[0-9]{7}|^\(0[1-9]{3}\)\s?[0-9]{6}|(\+31[0]?[1-9]{1}[0-9\s\-]{9})/',
        'be' => '/^((00|\+)32|0)(45[56]|46[05-8]|47\d|48\d|49\d)(\d{6})$/',
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
        $value = str_replace('-', '', $value);
        $value = str_replace(' ', '', $value);

        return false != preg_match($this->countryRegexes[$this->country], $value);
    }

    /**
     * Get the validation error message.
     */
    public function message(): string
    {
        return trans('validation.phone_number');
    }

    public function validate(string $attribute, mixed $value, \Closure $fail): void
    {
        if (! $this->passes($attribute, $value)) {
            $fail($this->message());
        }
    }
}
