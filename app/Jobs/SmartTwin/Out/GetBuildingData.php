<?php

namespace App\Jobs\SmartTwin\Out;

use App\Helpers\Hoomdossier;
use App\Helpers\Queue;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Services\SmartTwin\BuildingDataService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

/**
 * Fetches the basic data of a dwelling from SmartTwin's Building Data API and writes it for an
 * input source: building type, build year, surface and registered energy label.
 *
 * In a cooperation with SmartTwin this takes the place of EP-Online and of BAG's build year and
 * surface, at registration, at a reset and at an address change; and it runs again after every
 * import with advice (decision 2026-10-15-smarttwin-woninggegevens-building-data, B1, B9, B13).
 *
 * Deliberately not a NonHandleableJobAfterReset. That skips a job queued before the last reset, and
 * a reset only records itself once it is done, so a job queued by the reset itself is skipped as
 * soon as the reset runs into the next second. This data is about the dwelling, not about what
 * anyone answered: it is as true after a reset as before, and a reset is one of the moments to
 * fetch it.
 */
class GetBuildingData implements ShouldQueue
{
    use Queueable;

    public $tries = 3;

    public $backoff = 60;

    public function __construct(public Building $building, public InputSource $inputSource)
    {
        $this->onQueue(Queue::APP_EXTERNAL);
    }

    /**
     * Dispatches the job when the cooperation takes part in SmartTwin. The cooperation can be given
     * for a building that is not linked to it yet, as at registration.
     */
    public static function dispatchFor(Building $building, InputSource $inputSource, ?Cooperation $cooperation = null): void
    {
        if (Hoomdossier::smartTwinEnabledFor($cooperation ?? Hoomdossier::cooperationOf($building))) {
            static::dispatch($building, $inputSource);
        }
    }

    public function handle(BuildingDataService $buildingDataService): void
    {
        // The environment can have been switched off since the job was queued.
        if (! Hoomdossier::hasSmartTwinApi()) {
            return;
        }

        $buildingDataService->refresh($this->building, $this->inputSource);
    }

    public function failed(Throwable $exception): void
    {
        report($exception);
    }
}
