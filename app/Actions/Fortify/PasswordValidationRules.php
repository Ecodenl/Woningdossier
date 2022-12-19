<?php

namespace App\Actions\Fortify;

use App\Helpers\Hoomdossier;
use Laravel\Fortify\Rules\Password;

trait PasswordValidationRules
{
    /**
     * Get the validation rules used to validate passwords.
     *
     * @return array
     */
    protected function passwordRules()
    {
        return ['required', 'string', (new Password)->length(Hoomdossier::PASSWORD_LENGTH), 'confirmed'];
    }
}
