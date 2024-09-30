<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class AlphaSpace implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param mixed  $value
     */
    public function passes(string $attribute, $value): bool
    {
        return preg_match('/^[\pL\s]+$/u', $value);
    }

    /**
     * Get the validation error message.
     */
    public function message(): string
    {
        return __('validation.custom.alpha_space');
    }
}
