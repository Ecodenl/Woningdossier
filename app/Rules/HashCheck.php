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
     * @param string $hashToCheck
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
     * @param string $attribute
     * @param mixed  $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return Hash::check($value, $this->hashToCheck);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('validation.hash_check');
    }
}
