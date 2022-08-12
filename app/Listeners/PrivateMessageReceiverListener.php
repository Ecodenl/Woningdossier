<?php

namespace App\Listeners;

use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Models\Account;
use App\Models\Building;
use App\Models\InputSource;
use App\Models\PrivateMessage;
use App\Models\PrivateMessageView;
use App\Models\User;
use App\Services\BuildingCoachStatusService;

class PrivateMessageReceiverListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     *
     * @param object $event
     *
     * @return void
     */
    public function handle($event)
    {
        $authenticatedUser = $event->authenticatable;

        $user = null;
        if ($authenticatedUser instanceof Account) {
            $user = $authenticatedUser->users()->where('cooperation_id', $event->cooperation->id)->first();
        }

        $groupParticipants = PrivateMessage::getGroupParticipants($event->privateMessage->building_id);

        $buildingFromOwner = Building::find($event->privateMessage->building_id);
        $privateMessage = $event->privateMessage;

        $connectedCoachesForBuilding = BuildingCoachStatusService::getConnectedCoachesByBuildingId($event->privateMessage->building_id);

        // now we create for every group participant a privatemessageview
        foreach ($groupParticipants as $groupParticipant) {
            // check the group participant is the owner of the building and the send message is private
            $isMessagePrivateAndGroupParticipantOwnerFromBuilding = $buildingFromOwner->user_id == $groupParticipant->id && PrivateMessage::isPrivate($privateMessage);

            // check if the current group participant id added to the buildingCoachStatus
            // if so the $inputSourceId will be the coach input source id
            // if not, the group participant is a resident.
            if ($connectedCoachesForBuilding->contains('coach_id', $groupParticipant->id)) {
                $inputSourceId = InputSource::findByShort(InputSource::COACH_SHORT)->id;
            } else {
                $inputSourceId = InputSource::findByShort(InputSource::RESIDENT_SHORT)->id;
            }

            // this checks if the current participant of the "group / chat", is the current authenticated user.
            // because if so, we wont be creating a "unread message" (private message view)
            // (because the current authenticated user is the sender of the message, and does not need a notification about a message he send himself)

            if ($groupParticipant->id != $user->id && !$isMessagePrivateAndGroupParticipantOwnerFromBuilding) {
                PrivateMessageView::create([
                    'input_source_id' => $inputSourceId,
                    'private_message_id' => $event->privateMessage->id,
                    'user_id' => $groupParticipant->id,
                ]);
            }
        }


        // avoid unnecessary privateMessagesViews, we dont want to create a row for the user itself
        if ($user instanceof User && !$user->hasRoleAndIsCurrentRole(['coordinator'])) {
            // creata a privateMessageView for the cooperation itself
            // since a cooperation is not a 'participant' of a chat we need to create a row for the manually
            PrivateMessageView::create([
                'private_message_id' => $event->privateMessage->id,
                'to_cooperation_id' => $event->cooperation->id
            ]);
        }
    }
}
