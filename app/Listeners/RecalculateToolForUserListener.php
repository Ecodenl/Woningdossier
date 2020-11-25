<?php

namespace App\Listeners;

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
            'high-efficiency-boiler'
        ];

        if (in_array($event->step->short, $stepsWhichNeedRecalculation)) {
            // recalculate the tool for the given user
            $userId = $event->building->user->id;
            \Artisan::call('tool:recalculate', ['--user' => [$userId]]);
        }
    }
}
