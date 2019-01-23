<?php

namespace App\Listeners;

use App\Helpers\HoomdossierSession;
use App\Models\PrivateMessage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ParticipantAddedListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $participantFullName = $event->addedParticipant->getFullName();
        $message = __('woningdossier.cooperation.chat.messages.participant-added', ['participant' => $participantFullName]);

        // is_public is set to true, could be changed in the future.
        PrivateMessage::create([
            'is_public' => true,
            'from_user_id' => $event->addedParticipant->id,
            'from_user' => $participantFullName,
            'building_id' => $event->building->id,
            'message' => $message,
            'to_cooperation_id' => HoomdossierSession::getCooperation(),
        ]);
    }
}
