<?php

namespace App\Listeners;

use App\Events\BuildingStatusUpdated;
use App\Models\Status;
use App\Services\SmartTwin\WoonplanLock;

/**
 * The coach marks the advice trajectory done by setting the building's status to "Uitgevoerd"; that
 * hands the woonplan back to the resident. See WoonplanLock.
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
        }
    }
}
