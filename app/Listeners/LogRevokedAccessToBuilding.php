<?php

namespace App\Listeners;

use App\Events\UserRevokedAccessToHisBuilding;
use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Models\Log;
use App\Models\User;

class LogRevokedAccessToBuilding
{
    /**
     * Handle the event.
     */
    public function handle(UserRevokedAccessToHisBuilding $event): void
    {
        Log::create([
            'loggable_type' => User::class,
            'loggable_id' => Hoomdossier::user()->id,
            'building_id' => HoomdossierSession::getBuilding(),
            'message' => __('woningdossier.log-messages.user-revoked-access', [
                'full_name' => \App\Helpers\Hoomdossier::user()->getFullName(),
            ]),
        ]);
    }
}
