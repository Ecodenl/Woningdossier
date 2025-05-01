<?php

namespace App\Policies;

use App\Enums\ApiImplementation;
use App\Helpers\RoleHelper;
use App\Models\Account;
use App\Models\User;
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

    public function refreshRegulations(Account $account): bool
    {
        $user = $account->user();

        return $user instanceof User && $user->cooperation->getCountry()->supportsApi(ApiImplementation::LV_BAG);
    }

    public function disableTwoFactor(Account $account, Account $target): bool
    {
        return $account->user()->hasRoleAndIsCurrentRole(RoleHelper::ROLE_COOPERATION_ADMIN)
            || $account->id === $target->id;
    }
}
