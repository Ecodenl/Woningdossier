<?php

namespace App\Listeners;

use App\Events\Registered;
use App\Models\Log;
use App\Models\User;
use Carbon\Carbon;

class LogRegisteredUserListener
{
    /**
     * Handle the event.
     */
    public function handle(Registered $event): void
    {
        Log::create([
            'loggable_type' => User::class,
            'loggable_id' => $event->user->id,
            'building_id' => $event->user->building->id,
            'message' => __('woningdossier.log-messages.registered-user', [
                'full_name' => $event->user->getFullName(),
                'time' => Carbon::now(),
            ]),
        ]);
    }
}
