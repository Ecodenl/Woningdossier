<?php

namespace App\Actions\Fortify;

use App\Helpers\Hoomdossier;
use Laravel\Fortify\Rules\Password;

trait PasswordValidationRules
{
    /**
     * Get the validation rules used to validate passwords.
     */
    protected function passwordRules(): array
    {
        return ['required', 'string', (new Password)->length(Hoomdossier::PASSWORD_LENGTH), 'confirmed'];
    }
}
