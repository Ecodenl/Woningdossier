<?php

namespace App\Listeners;

use App\Events\PrivateMessageReceiverEvent;
use App\Helpers\RoleHelper;
use App\Models\Account;
use App\Models\Client;
use App\Models\InputSource;
use App\Models\PrivateMessage;
use App\Models\PrivateMessageView;
use App\Models\User;
use App\Services\BuildingCoachStatusService;

class PrivateMessageReceiverListener
{
    /**
     * Handle the event.
     */
    public function handle(PrivateMessageReceiverEvent $event): void
    {
        $authenticatedUser = $event->authenticatable;
        $isClient = $authenticatedUser instanceof Client;

        $user = null;
        if ($authenticatedUser instanceof Account) {
            $user = $authenticatedUser->users()
                ->forAllCooperations()
                ->where('cooperation_id', $event->cooperation->id)
                ->first();
        }

        $buildingFromOwner = $event->privateMessage->building;
        $privateMessage = $event->privateMessage;
        $groupParticipants = PrivateMessage::getGroupParticipants($buildingFromOwner);

        // TODO: This shouldn't generate a PMV for a coach input source because the coach works from a cooperation perspective
        $connectedCoachesForBuilding = BuildingCoachStatusService::getConnectedCoachesByBuilding($buildingFromOwner);

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
            $isGroupParticipantNonAuthenticatedUser = $user instanceof User && $groupParticipant->id != $user->id;

            if (! $isMessagePrivateAndGroupParticipantOwnerFromBuilding && ($isClient || $isGroupParticipantNonAuthenticatedUser)) {
                PrivateMessageView::create([
                    'input_source_id' => $inputSourceId,
                    'private_message_id' => $event->privateMessage->id,
                    'user_id' => $groupParticipant->id,
                ]);
            }
        }

        // avoid unnecessary privateMessagesViews, we dont want to create a row for the user itself
        if ($isClient || ($user instanceof User && ! $user->hasRoleAndIsCurrentRole([RoleHelper::ROLE_COACH, RoleHelper::ROLE_COORDINATOR, RoleHelper::ROLE_COOPERATION_ADMIN]))) {
            // Create a a privateMessageView for the cooperation itself
            // since a cooperation is not a 'participant' of a chat we need to create a row for the manually
            PrivateMessageView::create([
                'private_message_id' => $event->privateMessage->id,
                'to_cooperation_id' => $event->cooperation->id,
            ]);
        }
    }
}
