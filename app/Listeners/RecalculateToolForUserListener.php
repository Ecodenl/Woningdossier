<?php

namespace App\Listeners;

use App\Helpers\HoomdossierSession;
use App\Models\Notification;
use App\Models\Step;
use Illuminate\Support\Facades\Artisan;

class RecalculateToolForUserListener
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
     *
     * @param object $event
     *
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
            $inputSource = HoomdossierSession::getInputSource(true);

            // currently this listener will only be triggered on a event thats dispatched while NOT running in the cli
            // so we can safely access the input source from the session

            if ($event->building->hasCompleted(Step::findByShort('general-data')))
            Notification::setActive($event->building, $inputSource, true);

            // recalculate the tool for the given user
            $userId = $event->building->user->id;
            Artisan::call('tool:recalculate', ['--user' => [$userId], '--input-source' => [$inputSource->short]]);
        }
    }
}
