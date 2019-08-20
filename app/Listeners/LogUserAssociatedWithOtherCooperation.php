<?php

namespace App\Listeners;

use App\Models\Log;

class LogUserAssociatedWithOtherCooperation
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
        Log::create([
            'user_id' => $event->user->id,
            'building_id' => $event->user->building->id,
            'message' => __('woningdossier.log-messages.user-associated-with-other-cooperation', [
                'full_name' => $event->user->getFullName(),
                'cooperation_name' => $event->cooperation->name
            ]),
        ]);
    }
}