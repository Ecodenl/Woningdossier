<?php

namespace App\Policies;

use App\Helpers\RoleHelper;
use App\Models\CooperationMeasureApplication;
use App\Models\Account;
use Illuminate\Auth\Access\HandlesAuthorization;

class CooperationMeasureApplicationPolicy
{
    use HandlesAuthorization;

    public function delete(Account $account, CooperationMeasureApplication $cooperationMeasureApplication): bool
    {
        return $account->user()->hasRoleAndIsCurrentRole(RoleHelper::ROLE_COOPERATION_ADMIN) && $cooperationMeasureApplication->is_deletable;
    }
}
