<?php

namespace App\Jobs\SmartTwin\Out;

use App\Enums\SmartTwin\EventType;
use App\Helpers\Hoomdossier;
use App\Models\Building;
use App\Services\SmartTwin\AdviceResultStorage;
use App\Services\SmartTwin\Api\SmartTwinApi;
use App\Services\SmartTwin\SmartTwinService;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GetAdviceResults implements ShouldQueue, ShouldBeUnique
{
    use Queueable;

    public function __construct(
        protected array $callbackData,
        protected int $buildingId,
    )
    {
    }

    public function uniqueId(): string
    {
        $eventType = $this->callbackData['EventType'] ?? 'unknown';

        return "{$eventType}_{$this->buildingId}";
    }

    public function handle(SmartTwinApi $api, SmartTwinService $service, AdviceResultStorage $storage): void
    {
        if (! Hoomdossier::hasEnabledSmartTwinCalls()) {
            return;
        }

        $dossierId = $this->callbackData['DossierId'] ?? null;
        $eventType = EventType::tryFrom($this->callbackData['EventType'] ?? '');

        if (! $dossierId || ! $eventType) {
            return;
        }

        $results = match ($eventType) {
            EventType::COACH_SCAN_FINISHED    => $api->advice()->getAdvisorToolResults($dossierId),
            EventType::RESIDENT_SCAN_FINISHED => $api->advice()->getQuickScanResults($dossierId),
        };

        $building = Building::findOrFail($this->buildingId);

        // Phase 1: persist the raw response before mapping, so a mapping retry never
        // has to re-fetch. The fetch always returns the dossier's current state.
        $storage->storeRaw($building, $eventType, $results);

        $service->processResults($building, $results, $eventType);

        // TODO: gate this on a successful mapping once SmartTwinService::processResults()
        // signals success/failure. For now it clears unconditionally (processResults is a stub),
        // matching the previous behaviour.
        $building->finishSmartTwinCallback($eventType);
        $building->save();
    }
}
