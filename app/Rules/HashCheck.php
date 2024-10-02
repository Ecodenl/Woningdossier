<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Hash;

class HashCheck implements ValidationRule
{
    /**
     * The hashed string that will be matched against the given value.
     *
     * @var string
     */
    public $hashToCheck;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(string $hashToCheck)
    {
        $this->hashToCheck = $hashToCheck;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param mixed  $value
     */
    public function passes($attribute, $value): bool
    {
        return Hash::check($value, $this->hashToCheck);
    }

    /**
     * Get the validation error message.
     */
    public function message(): string
    {
        return __('validation.hash_check');
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $this->passes($attribute, $value)) {
            $fail($this->message());
        }
    }
}
