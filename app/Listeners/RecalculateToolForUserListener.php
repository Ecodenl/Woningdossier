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
     * @param  object  $event
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
            // default for recalculate it set at resident
            // yes we check for the master, but recalculate the resident.
            // we always insert / update to resident and retrieve the master.
            Artisan::call(RecalculateForUser::class, [
                '--user' => [$event->building->user->id], '--input-source' => [$event->inputSource->short]
            ]);
        }
    }
}
