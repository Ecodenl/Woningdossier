<?php

namespace App\Listeners;

use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Models\Log;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogAllowedAccessToBuilding
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
            'user_id'     => Hoomdossier::user()->id,
            'building_id' => HoomdossierSession::getBuilding(),
            'message'     => __('woningdossier.log-messages.user-gave-access', [
                'full_name' => \App\Helpers\Hoomdossier::user()->getFullName(),
            ]),
        ]);
    }
}
