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
        if ($user->hasRole(['cooperation-admin', 'coordinator'])) {
            return true;
//            foreach ($user->cooperations as $cooperation) {
//                if (in_array($cooperation->id, compact('sendingCooperationId', 'receivingCooperationId'))) {
//                    return true;
//                }
//            }
//            if (in_array($user->id, compact('sendingUserId', 'receivingUserId'))) {
//                return true;
//            }
//
//            return false;
        }
        //if ($user->hasRo)
        if ($user->hasRole(['coach', 'resident'])) {
            if (in_array(HoomdossierSession::getBuilding(), compact('buildingId'))) {
                return true;
            }

            return false;
        }
    }
}
