<?php

namespace App\Listeners;

use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Models\Building;
use App\Models\InputSource;
use App\Models\PrivateMessage;
use App\Models\PrivateMessageView;
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
        $groupParticipants = PrivateMessage::getGroupParticipants($event->privateMessage->building_id);

        $buildingFromOwner = Building::find($event->privateMessage->building_id);
        $privateMessage = PrivateMessage::find($event->privateMessage->id);

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

            // if the message is private and the group member is the owner, we dont notify him because the message is not intended for him
            if ($groupParticipant->id != Hoomdossier::user()->id && ! $isMessagePrivateAndGroupParticipantOwnerFromBuilding) {
                PrivateMessageView::create([
                    'input_source_id' => $inputSourceId,
                    'private_message_id' => $event->privateMessage->id,
                    'user_id' => $groupParticipant->id,
                ]);
            }
        }

        // avoid unnecessary privateMessagesViews, we dont want to create a row for the user itself
        if (! Hoomdossier::user()->hasRoleAndIsCurrentRole(['coordinator'])) {
            // since a cooperation is not a 'participant' of a chat we need to create a row for the manually
            PrivateMessageView::create([
                'private_message_id' => $event->privateMessage->id,
                'to_cooperation_id' => HoomdossierSession::getCooperation(),
            ]);
        }
    }
}
