<?php

namespace App\Services;

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
    // This will be used to determine the prefixes on the messages;
    const REQUEST_TYPE_COACH_CONVERSATION = 'coach-conversation';
    const REQUEST_TYPE_MEASURE = 'other';

    /**
     * Create a new message between a user and user.
     *
     * @return bool
     */
    public static function create(Request $request)
    {
        $message = strip_tags($request->get('message', ''));

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
                'from_user' => Hoomdossier::user()->getFullName(),
                'from_user_id' => Hoomdossier::user()->id,
                'message' => strip_tags($message),
                'building_id' => $buildingId,
            ];

            // users that have the role coordinator and cooperation admin dont talk fom them self but from a cooperation
            if (Hoomdossier::user()->hasRoleAndIsCurrentRole(['coordinator', 'cooperation-admin'])) {
                $privateMessageData['from_cooperation_id'] = HoomdossierSession::getCooperation();
                $privateMessageData['from_user'] = HoomdossierSession::getCooperation(true)->name;
            }

            PrivateMessage::create(
                $privateMessageData
            );
        }

        return true;
    }

    public static function getMessagePrefix(string $requestType)
    {
        $requestTypesThatAreTranslatable = array_flip([
            self::REQUEST_TYPE_COACH_CONVERSATION,
            self::REQUEST_TYPE_MEASURE,
        ]);

        return isset($requestTypesThatAreTranslatable[$requestType]) ? __('conversation-requests.request-types.'.$requestType) : null;
    }

    /**
     * Method to create a conversation request for a building on a user.
     */
    public static function createConversationRequest(Building $building, User $user, Request $request)
    {
        $message = strip_tags($request->get('message', ''));
        $measureApplicationName = $request->get('measure_application_name', null);
        $messagePrefix = self::getMessagePrefix($request->get('request_type', ''));
        $message = is_null($measureApplicationName) ? "<b>{$messagePrefix}: </b>$message" : "<b>{$measureApplicationName}: </b>{$message}";

        PrivateMessage::create(
            [
                // we get the selected option from the language file, we can do this cause the submitted value = key from localization
                'is_public'         => true,
                'from_user_id'      => $user->id,
                'from_user'         => $user->getFullName(),
                'message'           => $message,
                'to_cooperation_id' => $user->cooperation->id,
                'building_id'       => $building->id,
            ]
        );
    }
}
