<?php

namespace App\Listeners;

use App\Console\Commands\Tool\RecalculateForUser;
use App\Helpers\HoomdossierSession;
use App\Models\InputSource;
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
        // only the solar panel step has input that may effect every measure (such as the amount_electricity)
        $stepsWhichNeedRecalculation = [
            'solar-panels',
        ];

        // the lite-scan and quick-scan should always recalculate
        if (in_array($event->step->short, ['lite-scan', 'quick-scan'])) {
            $stepsWhichNeedRecalculation = $event->step->scan->steps->pluck('short');
        }

        if (in_array($event->step->short, $stepsWhichNeedRecalculation)) {
            $inputSource = HoomdossierSession::getInputSource(true);
            // Theres nothing to recalculate if the user did not complete the main step.

            if ($event->building->hasCompletedQuickScan(InputSource::findByShort(InputSource::MASTER_SHORT))) {
                $userId = $event->building->user->id;
                // default for recalculate it set at resident
                // yes we check for the master, but recalculate the resident.
                // we always insert / update to resident and retrieve the master.
                Artisan::call(RecalculateForUser::class, ['--user' => [$userId], '--input-source' => [$inputSource->short]]);
            }
        }
    }
}
