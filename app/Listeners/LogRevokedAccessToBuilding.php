<?php

namespace App\Listeners;

use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Models\Log;
use App\Models\User;

class LogRevokedAccessToBuilding
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
     */
    public function handle(object $event): void
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
