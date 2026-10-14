<?php

namespace App\Jobs\SmartTwin;

use App\Console\Commands\Tool\RecalculateForUser;
use App\Helpers\Queue;
use App\Helpers\ScanAvailabilityHelper;
use App\Models\Building;
use App\Models\InputSource;
use App\Models\Scan;
use App\Models\User;
use App\Services\Scans\ScanFlowService;
use App\Services\UserActionPlanAdviceService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

/**
 * Takes one building back to Hoomdossier without SmartTwin, after its cooperation was switched off.
 *
 * The order matters. The SmartTwin advices have to be gone before the recalculation: as long as a
 * building has one, the calculation does not add the advices SmartTwin replaced (see
 * UserActionPlanAdviceService::isOwnedExternally()). That check also asks the cooperation, which is
 * already switched off by the time this runs.
 */
class UndoSmartTwinForBuilding implements ShouldQueue
{
    use Queueable;

    public function __construct(public Building $building)
    {
        $this->onQueue(Queue::APP);
    }

    public function handle(): void
    {
        $building = $this->building;

        // 1. Results SmartTwin still has to send are not taken in anymore.
        $callbacks = $building->getSmartTwinCallbacks();
        if (! empty($callbacks)) {
            Log::info('SmartTwin switched off: dropping open callbacks', [
                'building_id' => $building->getKey(),
                'flows'       => array_keys($callbacks),
            ]);

            $building->smarttwin_callback = null;
            $building->save();
        }

        // 2. The SmartTwin advices go, for every input source.
        $removed = UserActionPlanAdviceService::removeSmartTwinAdvices($building);

        $user = $building->user()->forAllCooperations()->first();

        if (! $user instanceof User) {
            return;
        }

        // 3. What SmartTwin left open is asked again; what it answered stays answered.
        $quickScan = Scan::quick();

        if ($quickScan instanceof Scan && ScanAvailabilityHelper::isAvailableForBuilding($building, $quickScan)) {
            ScanFlowService::init($quickScan, $building, InputSource::resident())->reassessCompletion($user);
        }

        // 4. The calculation fills the woonplan again, for the resident and the coach.
        Artisan::call(RecalculateForUser::class, [
            '--user'             => [$user->id],
            '--input-source'     => [InputSource::RESIDENT_SHORT, InputSource::COACH_SHORT],
            '--with-old-advices' => true,
        ]);

        Log::info('SmartTwin switched off: building taken back to the calculation', [
            'building_id'     => $building->getKey(),
            'advices_removed' => $removed,
        ]);
    }
}
