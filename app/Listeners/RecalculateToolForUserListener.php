<?php

namespace App\Listeners;

use App\Helpers\HoomdossierSession;
use App\Models\Notification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class RecalculateToolForUserListener
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
        $stepsWhichNeedRecalculation = [
            'building-characteristics',
            'current-state',
            'usage',
            'interest',

            'high-efficiency-boiler',
            'solar-panels',
            'heater',
        ];

        if (in_array($event->step->short, $stepsWhichNeedRecalculation)) {

            // currently this listener will only be triggered on a event thats dispatched while NOT running in the cli
            // so we can safely access the input source from the session
            Notification::updateOrCreate([
                'input_source_id' => HoomdossierSession::getInputSource(),
                'type' => 'recalculate',
                // the building owner is always passed to the job.
                'building_id' => $event->building->user->id,
            ], ['is_active' => true]);

            // recalculate the tool for the given user
            $userId = $event->building->user->id;
            \Artisan::call('tool:recalculate', ['--user' => [$userId]]);
        }
    }
}
