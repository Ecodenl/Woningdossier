<?php

namespace App\Jobs\SmartTwin\Out;

use App\Enums\SmartTwin\EventType;
use App\Helpers\Hoomdossier;
use App\Models\Building;
use App\Services\SmartTwin\AdviceResultStorage;
use App\Services\SmartTwin\Api\SmartTwinApi;
use App\Services\SmartTwin\MappingReportStorage;
use App\Services\SmartTwin\SmartTwinService;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

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

    public function handle(
        SmartTwinApi $api,
        SmartTwinService $service,
        AdviceResultStorage $storage,
        MappingReportStorage $reportStorage,
    ): void
    {
        $dossierId = $this->callbackData['DossierId'] ?? null;
        $eventType = EventType::tryFrom($this->callbackData['EventType'] ?? '');

        if (! $dossierId || ! $eventType) {
            return;
        }

        if (! Hoomdossier::hasEnabledSmartTwinCalls()) {
            $this->drop($eventType, 'SmartTwin calls are disabled');

            return;
        }

        $results = match ($eventType) {
            EventType::COACH_SCAN_FINISHED    => $api->advice()->getAdvisorToolResults($dossierId),
            EventType::RESIDENT_SCAN_FINISHED => $api->advice()->getQuickScanResults($dossierId),
        };

        $building = Building::findOrFail($this->buildingId);

        // Persist the raw response before mapping, so a mapping retry never has to re-fetch.
        // The fetch always returns the dossier's current state.
        $storage->storeRaw($building, $eventType, $results);

        // The callback is a "still to be fetched" marker: it is what the nightly fallback cron
        // (api:smarttwin:get-advice-results) scans for. Once the response is on disk that is done,
        // so it is cleared here rather than after the mapping. A failed mapping is a separate
        // problem with a separate remedy — replay it from the stored response on the super admin
        // page — and must not make the cron pull the same results from SmartTwin all over again.
        $building->finishSmartTwinCallback($eventType);
        $building->save();

        $reportStorage->store($service->processResults($building, $results, $eventType, $dossierId));
    }

    /**
     * Give up on a callback we are not going to fetch. Returning without clearing it would leave the
     * marker in place, and the nightly fallback cron would pick the same callback up again every night.
     */
    private function drop(EventType $eventType, string $reason): void
    {
        Log::warning("SmartTwin callback dropped: {$reason}", [
            'building_id' => $this->buildingId,
            'flow'        => $eventType->value,
            'dossierId'   => $this->callbackData['DossierId'] ?? null,
        ]);

        $building = Building::find($this->buildingId);

        if ($building instanceof Building && $building->hasSmartTwinCallback($eventType)) {
            $building->finishSmartTwinCallback($eventType);
            $building->save();
        }
    }
}
