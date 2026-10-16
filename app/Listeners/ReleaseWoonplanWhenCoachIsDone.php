<?php

namespace App\Listeners;

use App\Events\BuildingStatusUpdated;
use App\Helpers\Hoomdossier;
use App\Jobs\SmartTwin\StoreReleasedWoonplanReport;
use App\Models\Status;
use App\Services\SmartTwin\WoonplanLock;

/**
 * The coach marks the advice trajectory done by setting the building's status to "Uitgevoerd"; that
 * hands the woonplan back to the resident. See WoonplanLock.
 *
 * A woonplan that is SmartTwin's also gets its report then, kept with the building's files and as
 * the coach's report (StoreReleasedWoonplanReport; decision 2026-10-16-smarttwin-rapport-bij-vrijgeven).
 */
class ReleaseWoonplanWhenCoachIsDone
{
    private const EXECUTED = 'executed';

    public function __construct(private readonly WoonplanLock $lock)
    {
    }

    public function handle(BuildingStatusUpdated $event): void
    {
        $status = $event->building->getMostRecentBuildingStatus()?->status;

        if ($status instanceof Status && self::EXECUTED === $status->short) {
            $this->lock->release($event->building);

            if (Hoomdossier::smartTwinOwnsWoonplanFor($event->building)) {
                StoreReleasedWoonplanReport::dispatch($event->building);
            }
        }
    }
}
