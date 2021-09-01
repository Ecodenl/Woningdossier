<?php

namespace App\Listeners;

use App\Console\Commands\Tool\RecalculateForUser;
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
            // Algemene gegevens
            'building-characteristics',
            'current-state',
            'usage',
            'interest',

            // Quick scan (Replaces algemene gegevens so must trigger a recalculate!)
            'building-data',
            'usage-quick-scan',
            'living-requirements',
            'residential-status',

            // Expert tool relevant steps
            'high-efficiency-boiler',
            'solar-panels',
            'heater',
        ];

        // TODO: Make this work with the master input source. Perhaps pass input source in event?
        if (in_array($event->step->short, $stepsWhichNeedRecalculation)) {
            // Currently this listener will only be triggered on a event that's dispatched while NOT running in the cli
            // so we can safely access the input source from the session
            $inputSource = HoomdossierSession::getInputSource(true);

            // Theres nothing to recalculate if the user did not complete the main step.
            if ($event->building->hasCompletedQuickScan()) {
                $userId = $event->building->user->id;
                Artisan::call(RecalculateForUser::class, ['--user' => [$userId], '--input-source' => [$inputSource->short]]);
            }
        }
    }
}
