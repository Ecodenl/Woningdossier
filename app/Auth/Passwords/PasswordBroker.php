<?php

namespace App\Auth\Passwords;

use Illuminate\Auth\Passwords\PasswordBroker as BasePasswordBroker;

class PasswordBroker extends BasePasswordBroker
{
    /**
     * Validate a password reset for the given credentials.
     *
     * @return \Illuminate\Contracts\Auth\CanResetPassword|string
     */
    public function validateReset(array $credentials)
    {
        if (is_null($user = $this->getUser($credentials))) {
            return static::INVALID_USER;
        }

        if (! $this->tokens->exists($user, $credentials['token'])) {
            return static::INVALID_TOKEN;
        }

        return $user;
    }
}
