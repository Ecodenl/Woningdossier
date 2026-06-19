<?php

namespace App\Jobs\SmartTwin\Out;

use App\Enums\SmartTwin\EventType;
use App\Helpers\Hoomdossier;
use App\Models\Building;
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
    ) {
    }

    public function uniqueId(): string
    {
        $eventType = $this->callbackData['EventType'] ?? 'unknown';

        return "{$eventType}_{$this->buildingId}";
    }

    public function handle(SmartTwinApi $api, SmartTwinService $service): void
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

        $service->processResults($building, $results, $eventType);

        $remaining = array_values(array_filter(
            $building->getSmartTwinCallbacks(),
            fn($cb) => ($cb['EventType'] ?? null) !== $eventType->value,
        ));

        $building->smarttwin_callback = empty($remaining) ? null : $remaining;
        $building->save();
    }
}
