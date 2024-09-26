<?php

namespace App\Policies;

use App\Helpers\HoomdossierSession;
use App\Helpers\RoleHelper;
use App\Models\Account;
use App\Models\Cooperation;
use Illuminate\Auth\Access\HandlesAuthorization;

class CooperationPolicy
{
    use HandlesAuthorization;

    public function create(Account $account)
    {
        return HoomdossierSession::currentRoleIs(RoleHelper::ROLE_SUPER_ADMIN);
    }

    public function update(Account $account, Cooperation $cooperation): bool
    {
        return HoomdossierSession::currentRoleIs(RoleHelper::ROLE_SUPER_ADMIN);
    }

    public function delete(Account $account, Cooperation $cooperation)
    {
        // hoom mag niet.
        if ('hoom' !== $cooperation->slug) {
            return HoomdossierSession::currentRoleIs(RoleHelper::ROLE_SUPER_ADMIN);
        }

        return false;
    }
}
