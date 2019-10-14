<?php

namespace App\Auth\Passwords;

use Illuminate\Auth\Passwords\PasswordBroker as BasePasswordBroker;
use Illuminate\Contracts\Auth\CanResetPassword;

class PasswordBroker extends BasePasswordBroker
{
    /**
     * Validate a password reset for the given credentials.
     *
     * @param  array  $credentials
     * @return \Illuminate\Contracts\Auth\CanResetPassword|string
     */
    public function validateReset(array $credentials)
    {
        if (is_null($user = $this->getUser($credentials))) {
            return static::INVALID_USER;
        }

        if (! $this->validateNewPassword($credentials)) {
            return static::INVALID_PASSWORD;
        }

        if (! $this->tokens->exists($user, $credentials['token'])) {
            return static::INVALID_TOKEN;
        }

        return $user;
    }

}