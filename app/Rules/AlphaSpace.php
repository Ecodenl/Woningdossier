<?php

namespace App\Rules;

// use Illuminate\Contracts\Validation\Rule;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class AlphaSpace implements ValidationRule
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
    public function passes($attribute, $value): bool
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

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $this->passes($attribute, $value)) {
            $fail($this->message());
        }
    }
}
