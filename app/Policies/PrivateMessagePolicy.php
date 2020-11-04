<?php

namespace App\Policies;

use App\Helpers\HoomdossierSession;
use App\Helpers\RoleHelper;
use App\Models\PrivateMessage;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PrivateMessagePolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    public function show(User $user)
    {
        // When a user is a resident and admin, we want to deny them.
        // this is because: a resident could be sending a message to the cooperation
        // he switches to coordinator, sees an unread message, but cant open it because a coordinator cant edit his own building.
        if ($user->hasRoleAndIsCurrentRole('resident') && $user->hasMultipleRoles()) {
            return false;
        }
        return true;
    }

    /**
     * Determine if the given message can be edited by the user.
     *
     * @return bool
     */
    public function edit(User $user, PrivateMessage $message)
    {
        // get the building id from the message
        $buildingId = $message->building_id;

        // note the order
        if ($user->hasRoleAndIsCurrentRole(['coordinator', 'coordinator-admin'])) {
            return true;
        }

        if ($user->hasRoleAndIsCurrentRole(['coach'])) {
            return $user->isNotRemovedFromBuildingCoachStatus($buildingId);
        }

        if ($user->hasRoleAndIsCurrentRole(['resident'])) {
            if (in_array(HoomdossierSession::getBuilding(), compact('buildingId'))) {
                return true;
            }
        }
    }
}
