<?php

namespace App\Auth\Passwords;

use Illuminate\Auth\Passwords\DatabaseTokenRepository as BaseDatabaseTokenRepository;
use Illuminate\Auth\Passwords\TokenRepositoryInterface;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class DatabaseTokenRepository extends BaseDatabaseTokenRepository implements TokenRepositoryInterface
{
    /**
     * Create a new token record.
     */
    public function create(CanResetPasswordContract $user): string
    {
        $email = $user->getEmailForPasswordReset();

        // Unlike the vendor, we do not delete the old token!

        // We will create a new, random token for the user so that we can e-mail them
        // a safe link to the password reset form. Then we will insert a record in
        // the database so that we can verify the token within the actual reset.
        $token = $this->createNewToken();

        $this->getTable()->insert($this->getPayload($email, $token));

        return $token;
    }

    /**
     * Determine if a token record exists and is valid.
     */
    public function exists(CanResetPasswordContract $user, $token): bool
    {
        // retrieve all the password resets
        $records = $this->getTable()->where(
            'email',
            $user->getEmailForPasswordReset()
        )->get();

        // Loop all tokens because there could be more than one for this e-mail. We can't just check the first
        // like the vendor, because that one might not be the one we're looking for.
        foreach ($records as $record) {
            if (! $this->tokenExpired($record->created_at) && $this->hasher->check($token, $record->token)) {
                return true;
            }
        }

        return false;
    }
}
