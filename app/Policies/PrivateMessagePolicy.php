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

        // TODO: refactor function
        $sendingUserId = $message->from_user_id;
        $receivingUserId = $message->to_user_id;
        $sendingCooperationId = $message->from_cooperation_id;
        $receivingCooperationId = $message->to_cooperation_id;

        $buildingId = $message->building_id;
        // note the order
        if ($user->hasRole(['cooperation-admin', 'coordinator']) && $message->to_cooperation_id == HoomdossierSession::getCooperation()) {

            return true;
        }

        if ($user->hasRole(['resident'])) {
            if (in_array(HoomdossierSession::getBuilding(), compact('buildingId'))) {
                return true;
            }

            return false;
        } elseif ($user->hasRole('coach') && $user->isNotRemovedFromBuildingCoachStatus($buildingId)) {

            return true;

        }
    }
}
