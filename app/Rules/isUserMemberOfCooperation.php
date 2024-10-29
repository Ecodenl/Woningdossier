<?php

namespace App\Rules;

use App\Models\User;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Contracts\Validation\ValidationRule;

class isUserMemberOfCooperation implements ValidationRule
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
     * Passes if the given user id has a relation with the current cooperation.
     *
     * @param mixed  $value
     */
    public function passes($attribute, $value): bool
    {
        $currentCooperation = \App\Helpers\Hoomdossier::user()->cooperations()->find(session('cooperation'));

        if ($currentCooperation->users()->find($value) instanceof User) {
            return true;
        }

        return false;
    }

    /**
     * Get the validation error message.
     */
    public function message(): string
    {
        return __('validation.custom.is-user-member-of-cooperation');
    }

    public function validate(string $attribute, mixed $value, \Closure $fail): void
    {
        if (! $this->passes($attribute, $value)) {
            $fail($this->message());
        }
    }
}
