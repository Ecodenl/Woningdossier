<?php

namespace App\Listeners;

use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Models\Log;
use Carbon\Carbon;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class StepDataHasBeenChangedListener implements ShouldQueue
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
            'building_id' => $event->building->id,
            'message' => __('woningdossier.log-messages.step-data-has-been-changed', [
                'full_name' => $event->user->getFullName(),
                'time' => Carbon::now(),
            ]),
        ]);
    }
}
