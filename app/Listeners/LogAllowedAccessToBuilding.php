<?php

namespace App\Listeners;

use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Models\Log;
use App\Models\User;

class LogAllowedAccessToBuilding
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
        /** @var User $user */
        $user = $event->building->user;
        Log::create([
            'user_id'     => $user->id,
            'building_id' => $event->building->id,
            'message'     => __('woningdossier.log-messages.user-gave-access', [
                'full_name' => $user->getFullName(),
            ]),
        ]);
    }
}
