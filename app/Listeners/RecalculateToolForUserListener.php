<?php

namespace App\Listeners;

use App\Console\Commands\Tool\RecalculateForUser;
use App\Helpers\HoomdossierSession;
use App\Models\InputSource;
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

        if (in_array($event->step->short, $stepsWhichNeedRecalculation)) {

            // Theres nothing to recalculate if the user did not complete the main step.
            if ($event->building->hasCompletedQuickScan(InputSource::findByShort(InputSource::MASTER_SHORT))) {
                $userId = $event->building->user->id;
                // default for recalculate it set at master
                Artisan::call(RecalculateForUser::class, ['--user' => [$userId]]);
            }
        }
    }
}
