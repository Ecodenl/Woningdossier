<?php

namespace App\Listeners;

use App\Events\SmartTwinDisabledForCooperation;
use App\Helpers\Queue;
use App\Jobs\SmartTwin\UndoSmartTwinForBuilding;
use App\Models\Building;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;

/**
 * Switching SmartTwin off for a cooperation takes every one of its buildings back to Hoomdossier
 * without SmartTwin. Queued, and one job per building: a cooperation can hold thousands, and the
 * super admin who flipped the switch should not wait for them.
 */
class UndoSmartTwinForCooperation implements ShouldQueue
{
    public $queue = Queue::APP;

    public function handle(SmartTwinDisabledForCooperation $event): void
    {
        $event->cooperation->buildings()->chunkById(100, function (Collection $buildings) {
            $buildings->each(fn (Building $building) => UndoSmartTwinForBuilding::dispatch($building));
        });
    }
}
