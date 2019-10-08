<?php

namespace App\Services;

use App\Events\UserAllowedAccessToHisBuilding;
use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\PrivateMessage;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class PrivateMessageService
{
    /**
     * Create a new message between a user and user.
     *
     * @param Request $request
     *
     * @return bool
     */
    public static function create(Request $request)
    {
        $message = $request->get('message', '');

        $isPublic = $request->get('is_public', true);
        $buildingId = $request->get('building_id', '');

        // if the is public is set to false
        // and the user current role is resident, then something isnt right.
        // since a resident cant access the private group chat
        if (! $isPublic && 'resident' == HoomdossierSession::getRole(true)->name) {
            return redirect()->back();
        }

        $building = Building::find($buildingId);

        // if the building exist create a message
        if ($building instanceof Building) {
            $privateMessageData = [
                'is_public' => $isPublic,
                'to_cooperation_id' => HoomdossierSession::getCooperation(),
                'from_user' => \App\Helpers\Hoomdossier::user()->getFullName(),
                'from_user_id' => Hoomdossier::user()->id,
                'message' => $message,
                'building_id' => $buildingId,
            ];

            // users that have the role coordinator and cooperation admin dont talk from themself but from a cooperation
            if (\App\Helpers\Hoomdossier::user()->hasRoleAndIsCurrentRole(['coordinator', 'cooperation-admin'])) {
                $privateMessageData['from_cooperation_id'] = HoomdossierSession::getCooperation();
                $privateMessageData['from_user'] = HoomdossierSession::getCooperation(true)->name;
            }

            PrivateMessage::create(
                $privateMessageData
            );
        }

        return true;
    }

    /**
     * Method to create a conversation request for a user.
     *
     * @param User $user
     * @param Request $request
     */
    public static function createConversationRequest(User $user, Request $request)
    {
        $action      = $request->get('action', '');
        $message     = strip_tags($request->get('message', ''));
        $measureApplicationName = $request->get('measure_application_name', null);
        $allowAccess = 'on' == $request->get('allow_access', '');
        $message = is_null($measureApplicationName) ? $message : "<b>{$measureApplicationName}: </b>{$message}";

        PrivateMessage::create(
            [
                // we get the selected option from the language file, we can do this cause the submitted value = key from localization
                'is_public'         => true,
                'from_user_id'      => Hoomdossier::user()->id,
                'from_user'         => $user->getFullName(),
                'message'           => $message,
                'to_cooperation_id' => $user->cooperation->id,
                'building_id'       => $user->building->id,
                'request_type'      => $action,
                'allow_access'      => $allowAccess,
            ]
        );

        // if the user allows access to his building on the request, log the activity.
        if ($allowAccess) {
            event(new UserAllowedAccessToHisBuilding());
        }
    }
}
