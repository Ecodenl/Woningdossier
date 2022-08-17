<?php

namespace App\Listeners;

use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Models\Log;
use App\Models\PrivateMessage;
use App\Models\User;
use Carbon\Carbon;

class ParticipantAddedListener
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

        Log::create([
            'loggable_type' => User::class,
            'loggable_id' => Hoomdossier::user()->id,
            'building_id' => $event->building->id,
            'message' => __('woningdossier.log-messages.participant-added', [
                'full_name' => \App\Helpers\Hoomdossier::user()->getFullName(),
                'for_full_name' => $participantFullName,
                'time' => Carbon::now(),
            ]),
        ]);
    }
}
