<?php

namespace App\Policies;

use App\Helpers\HoomdossierSession;
use App\Models\Building;
use App\Models\BuildingCoachStatus;
use App\Models\PrivateMessage;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Ramsey\Uuid\Uuid;

class UserPolicy
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
     * Check if a user is authorized to do admin stuff.
     *
     * @param User $user
     *
     * @return bool
     */
    public function accessAdmin(User $user): bool
    {
        if ($user->hasAnyRole(['coordinator', 'super-user', 'coach', 'cooperation-admin'])) {
            return true;
        }

        return false;
    }

    /**
     * Check if a user is authorized to respond.
     *
     * @param User $user
     * @param $buildingId
     *
     * @return bool
     */
    public function respond(User $user, $buildingId): bool
    {
        // todo refactor
//        return true;

        // we pass the building id from the chat
        // so if if the building id is not like the building set in the sessions its prolly a admin
        if ($buildingId != HoomdossierSession::getBuilding()) {

        }

        if ($user->can(['access-admin'])) {

            if ($user->hasRole('coach') && $user->isNotRemovedFromBuildingCoachStatus($buildingId)) {
                return true;
            } elseif($user->hasRole(['coordinator'])) {
                return false;
            }

        } else {
            // a resident can always respond
            return true;

        }
//        $mainMessage = PrivateMessage::find($mainMessageId);
//        $receiveUser = User::find($mainMessage->to_user_id);
//        $sendUser = User::find($mainMessage->from_user_id);
//
//        if ($sendUser->can('access-admin') && $receiveUser->can('access-admin')) {
//            return true;
//        } else {
//            // if the to user id is empty, its probbaly a message thats send to the cooperation
//            if (empty($mainMessage->to_user_id)) {
//                return true;
//            }
//            // this is NOT the request to the cooperation.
//            // this is the mainMessage from the current chat with resident and coach
//            $building = Building::where('user_id', $mainMessage->to_user_id)->first();
//
//            // either the coach or the coordinator, or someone with a higher role then resident.
//            $fromId = $mainMessage->from_user_id;
//            // get the most recent building coach status
//            $buildingCoachStatus = BuildingCoachStatus::where('coach_id', $fromId)->where('building_id', $building->id)->get()->last();
//
//            if (BuildingCoachStatus::STATUS_REMOVED == $buildingCoachStatus->status) {
//                return false;
//            }
//
//            return true;
//        }
    }

    /**
     * Check if a user is authorized to make an appointment.
     *
     * @param User $user
     * @param $buildingId
     *
     * @return bool
     */
    public function makeAppointment(User $user, $buildingId): bool
    {
        if ($user->can('access-admin')) {
            // get the last known coach status for the current coach
            $buildingCoachStatus = BuildingCoachStatus::where('coach_id', $user->id)->where('building_id', $buildingId)->get()->last();

            // if the coach his last known building status for the current building is removed
            // we return false, the user either removed the coach or the coach did this himself
            if (BuildingCoachStatus::STATUS_REMOVED == $buildingCoachStatus->status) {
                return false;
            }

            // if the status is not removed we grant access
            return true;
        }

        return false;
    }



    /**
     * Check if a user is allowed to participate in a group chat or not
     *
     * @param User $user
     * @param $buildingId
     * @return bool
     */
    public function participateInGroupChat(User $user, $buildingId): bool
    {
        // if the user is a coach and has a active building coach status, return true
        if ($user->hasRole('coach') && $user->isNotRemovedFromBuildingCoachStatus($buildingId)) {
            return true;
        } elseif ($user->hasRole('resident') && HoomdossierSession::getBuilding() == $buildingId) {
            return true;
        }

        return false;
    }


    /**
     * Check if a user can remove a participant from the group chat
     *
     * @param User $user | Auth user
     * @param User $groupParticipant | Participant from the group chat
     * @return bool
     */
    public function removeParticipantFromChat(User $user, User $groupParticipant): bool
    {
        // a coordinator and resident can remove a coach from a conversation
        if ($user->hasRole(['resident', 'coordinator']) && $groupParticipant->hasRole(['coach'])) {
            return true;
        }

        return false;
    }

    /**
     * Check if a user is authorized to access a building.
     * We check if the user (that is an admin), is authorized to fill / has access to a other building.
     *
     * @param User $user
     * @param $buildingId
     *
     * @return bool
     */
    public function accessBuilding(User $user, $buildingId): bool
    {
        $buildingCoachStatus = BuildingCoachStatus::where('building_id', $buildingId)->where('coach_id', $user->id)->get()->last();
        $conversationRequest = PrivateMessage::where('building_id', $buildingId)
            ->where('request_type','!=', null)
            ->first();

        if ($user->can('access-admin') && ($user->hasBuildingPermission($buildingId) && $conversationRequest->allow_access)) {
            return true;
        }

        return false;
    }
}
