<?php

namespace App\Jobs\SmartTwin;

use App\Enums\SmartTwin\EventType;
use App\Models\Building;
use App\Services\SmartTwin\AdviceResultStorage;
use App\Services\SmartTwin\MappingReportStorage;
use App\Services\SmartTwin\SmartTwinService;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

/**
 * Run the mapping again on the raw response that was already stored for a building + flow, without
 * re-hitting the SmartTwin API. Used to iterate on the mapping: tweak it, replay, compare output.
 */
class ProcessAdviceResults implements ShouldQueue, ShouldBeUnique
{
    use Queueable;

    public function __construct(
        protected int $buildingId,
        protected EventType $eventType,
    )
    {
    }

    public function uniqueId(): string
    {
        return "{$this->eventType->value}_{$this->buildingId}";
    }

    public function handle(
        SmartTwinService $service,
        AdviceResultStorage $storage,
        MappingReportStorage $reportStorage,
    ): void
    {
        $building = Building::findOrFail($this->buildingId);
        $results = $storage->readRaw($building, $this->eventType);

        if (is_null($results)) {
            return;
        }

        $reportStorage->store($service->processResults($building, $results, $this->eventType));
    }
}
