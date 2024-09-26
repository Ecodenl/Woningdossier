<?php

namespace App\Policies;

use App\Helpers\HoomdossierSession;
use App\Helpers\RoleHelper;
use App\Models\Account;
use App\Models\PrivateMessage;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PrivateMessagePolicy
{
    use HandlesAuthorization;

    public function isAllowed(Account $account, PrivateMessage|string|null $message)
    {
        $user = $account->user();

        // note the order
        if ($user->hasRoleAndIsCurrentRole([RoleHelper::ROLE_COORDINATOR, RoleHelper::ROLE_COOPERATION_ADMIN])) {
            return true;
        }

        if (! $message instanceof PrivateMessage) {
            return false;
        }

        // get the building id from the message
        $buildingId = $message->building_id;

        if ($user->hasRoleAndIsCurrentRole([RoleHelper::ROLE_COACH])) {
            return $user->isNotRemovedFromBuildingCoachStatus($buildingId);
        }

        if ($user->hasRoleAndIsCurrentRole([RoleHelper::ROLE_RESIDENT])) {
            if (in_array(HoomdossierSession::getBuilding(), compact('buildingId'))) {
                return true;
            }
        }

        return false;
    }

    public function view(Account $account)
    {
        return $this->isAllowed($account, PrivateMessage::class);
    }

    public function edit(Account $account, PrivateMessage $message)
    {
        return $this->isAllowed($account, $message);
    }
}
