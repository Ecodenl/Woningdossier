<?php

namespace App\Policies;

use App\Helpers\RoleHelper;
use App\Models\Account;
use Illuminate\Auth\Access\HandlesAuthorization;

class AccountPolicy
{
    use HandlesAuthorization;

    public function verifyEmail(Account $account, Account $target): bool
    {
        // A user can verify their own email, and certain roles can verify a (resident's) account's email also.
        return is_null($target->email_verified_at) && ($account->id === $target->id || ($account->user()->hasRole([
            RoleHelper::ROLE_COACH,
            RoleHelper::ROLE_COORDINATOR,
            RoleHelper::ROLE_COOPERATION_ADMIN,
        ])) && $target->user()->hasRole(RoleHelper::ROLE_RESIDENT));
    }
}
