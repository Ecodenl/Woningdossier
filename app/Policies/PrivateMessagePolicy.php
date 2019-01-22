<?php

namespace App\Policies;

use App\Helpers\HoomdossierSession;
use App\Models\PrivateMessage;
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

    /**
     * Determine if the given message can be edited by the user.
     *
     * @param User           $user
     * @param PrivateMessage $message
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
