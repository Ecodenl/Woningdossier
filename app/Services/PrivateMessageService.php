<?php

namespace App\Services;

use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Models\Building;
use App\Models\PrivateMessage;
use App\Models\User;
use Illuminate\Http\Request;

class PrivateMessageService
{
    // This will be used to determine the prefixes on the messages;
    const string REQUEST_TYPE_COACH_CONVERSATION = 'coach-conversation';
    const string REQUEST_TYPE_MEASURE = 'other';

    /**
     * Create a new message between a user and user.
     */
    public static function create(Request $request): void
    {
        $message = strip_tags($request->get('message', ''));

        $isPublic = $request->get('is_public', true);
        $buildingId = $request->get('building_id', '');

        // if the is public is set to false
        // and the user current role is resident, then something isn't right.
        // since a resident cant access the private group chat
        if (! $isPublic && 'resident' == HoomdossierSession::getRole(true)->name) {
            return;
        }

        $building = Building::find($buildingId);

        // if the building exist create a message
        if ($building instanceof Building) {
            $cooperation = HoomdossierSession::getCooperation(true);
            $privateMessageData = [
                'is_public' => $isPublic,
                'to_cooperation_id' => $cooperation->id,
                'from_user' => Hoomdossier::user()->getFullName(),
                'from_user_id' => Hoomdossier::user()->id,
                'message' => strip_tags($message),
                'building_id' => $buildingId,
            ];

            // users that have the role coordinator and cooperation admin dont talk fom them self but from a cooperation
            if (Hoomdossier::user()->hasRoleAndIsCurrentRole(['coordinator', 'cooperation-admin'])) {
                $privateMessageData['from_cooperation_id'] = $cooperation->id;
                $privateMessageData['from_user'] = $cooperation->name;
            }

            PrivateMessage::create(
                $privateMessageData
            );
        }
    }

    public static function getMessagePrefix(string $requestType): ?string
    {
        $requestTypesThatAreTranslatable = array_flip([
            self::REQUEST_TYPE_COACH_CONVERSATION,
            self::REQUEST_TYPE_MEASURE,
        ]);

        return isset($requestTypesThatAreTranslatable[$requestType])
            ? __('conversation-requests.request-types.' . $requestType)
            : null;
    }

    /**
     * Method to create a conversation request for a building on a user.
     */
    public static function createConversationRequest(Building $building, User $user, Request $request): void
    {
        $message = strip_tags($request->get('message', ''));
        $measureApplicationName = $request->get('measure_application_name', null);
        $messagePrefix = self::getMessagePrefix($request->get('request_type', ''));
        $message = is_null($measureApplicationName)
            ? "<b>{$messagePrefix}: </b>$message"
            : "<b>{$measureApplicationName}: </b>{$message}";

        PrivateMessage::create(
            [
                // We get the selected option from the language file. We can do this because
                // the submitted value = key from localization
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
