<?php

namespace App\Policies;

use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Helpers\RoleHelper;
use App\Models\Account;
use App\Models\PrivateMessage;
use Illuminate\Auth\Access\HandlesAuthorization;

class PrivateMessagePolicy
{
    use HandlesAuthorization;

    public function viewAny(Account $account): bool
    {
        return ! Hoomdossier::user()->isFillingToolForOtherBuilding() && ! Hoomdossier::user()->hasRoleAndIsCurrentRole(['super-admin','superuser']);
    }

    public function update(Account $account, PrivateMessage $message): bool
    {
        $user = $account->user();

        // note the order
        if ($user->hasRoleAndIsCurrentRole([RoleHelper::ROLE_COORDINATOR, RoleHelper::ROLE_COOPERATION_ADMIN, RoleHelper::ROLE_SUPER_ADMIN])) {
            return true;
        }

        // get the building id from the message
        $buildingId = $message->building_id;

        if ($user->hasRoleAndIsCurrentRole([RoleHelper::ROLE_COACH])) {
            return $user->isNotRemovedFromBuildingCoachStatus($buildingId);
        } elseif ($user->hasRoleAndIsCurrentRole([RoleHelper::ROLE_RESIDENT])) {
            if (HoomdossierSession::getBuilding(false) === $buildingId) {
                return true;
            }
        }
        return false;
    }
}
