<?php

namespace App\Auth\Passwords;

use Illuminate\Auth\Passwords\DatabaseTokenRepository as BaseDatabaseTokenRepository;
use Illuminate\Auth\Passwords\TokenRepositoryInterface;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class DatabaseTokenRepository extends BaseDatabaseTokenRepository implements TokenRepositoryInterface
{
    /**
     * Create a new token record.
     *
     * @return string
     */
    public function create(CanResetPasswordContract $user)
    {
        $email = $user->getEmailForPasswordReset();

        // We will create a new, random token for the user so that we can e-mail them
        // a safe link to the password reset form. Then we will insert a record in
        // the database so that we can verify the token within the actual reset.
        $token = $this->createNewToken();

        $this->getTable()->insert($this->getPayload($email, $token));

        return $token;
    }

    /**
     * Determine if a token record exists and is valid.
     *
     * @param string $token
     *
     * @return bool
     */
    public function exists(CanResetPasswordContract $user, $token)
    {
        // retrieve all the password resets
        $records = $this->getTable()->where(
                'email', $user->getEmailForPasswordReset()
        )->get();

        foreach ($records as $record) {
            if (! $this->tokenExpired($record->created_at) && $this->hasher->check($token, $record->token)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if the given user recently created a password reset token.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @return bool
     */
    public function recentlyCreatedToken(CanResetPasswordContract $user)
    {
        $record = (array) $this->getTable()->where(
            'email', $user->getEmailForPasswordReset()
        )->first();

        return $record && $this->tokenRecentlyCreated($record['created_at']);
    }

}
