<?php

namespace App\Listeners;

use App\Helpers\HoomdossierSession;
use App\Models\Log;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogRevokedAccessToBuilding
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
            'user_id' => \Auth::id(),
            'building_id' => HoomdossierSession::getBuilding(),
            'message' => __('woningdossier.log-messages.user-revoked-access', [
                'full_name' => \Auth::user()->getFullName(),
            ]),
        ]);
    }
}
