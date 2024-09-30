<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class HashCheck implements Rule
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
    public function passes(string $attribute, $value): bool
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
}
