<?php

namespace App\Listeners;

use App\Helpers\HoomdossierSession;
use App\Models\Account;
use App\Models\Client;
use App\Models\Log;
use App\Models\PrivateMessage;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\Authenticatable;

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

        // TODO: If we need to check authenticatable more often than just here, move to a LogHelper or -Service
        if (($authenticatable = $event->authenticatable) instanceof Authenticatable) {
            if ($authenticatable instanceof Account) {
                // We prefer logging the user, so we attempt fetching the user and use that if available
                $user = $authenticatable->user();
                if ($user instanceof User) {
                    $authenticatable = $user;
                    $name = $user->getFullName();
                } else {
                    // Fallback to account email
                    $name = $authenticatable->email;
                }
            } elseif ($authenticatable instanceof Client) {
                $name = $authenticatable->name;
            }

            $name = $name ?? 'unknown';

            Log::create([
                'loggable_type' => get_class($authenticatable),
                'loggable_id' => $authenticatable->id,
                'building_id' => $event->building->id,
                'message' => __('woningdossier.log-messages.participant-added', [
                    'full_name' => $name,
                    'for_full_name' => $participantFullName,
                    'time' => Carbon::now(),
                ]),
            ]);
        }
    }
}
