<?php

namespace App\Listeners;

use App\Events\UserAllowedAccessToHisBuilding;
use App\Models\Log;
use App\Models\User;

class LogAllowedAccessToBuilding
{
    /**
     * Handle the event.
     */
    public function handle(UserAllowedAccessToHisBuilding $event): void
    {
        /** @var User $user */
        $user = $event->user;
        Log::create([
            'loggable_type' => User::class,
            'loggable_id' => $user->id,
            'building_id' => $event->building->id,
            'message' => __('woningdossier.log-messages.user-gave-access', [
                'full_name' => $user->getFullName(),
            ]),
        ]);
    }
}
