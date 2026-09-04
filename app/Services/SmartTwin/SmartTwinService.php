<?php

namespace App\Services\SmartTwin;

use App\Enums\SmartTwin\EventType;
use App\Models\Building;
use App\Services\SmartTwin\Mapping\Leaf;
use App\Services\SmartTwin\Mapping\MapperRegistry;
use App\Services\SmartTwin\Mapping\MappingReport;
use App\Services\SmartTwin\Mapping\ResponseFlattener;
use Illuminate\Support\Facades\Log;
use Throwable;

class SmartTwinService
{
    public function __construct(
        private readonly ResponseFlattener $flattener,
        private readonly MapperRegistry $registry,
    )
    {
    }

    /**
     * Map one response onto the building, and report on every field it contained.
     *
     * The walk runs over the response, not over the mappers we happen to have: that is what keeps
     * the report complete, and what makes a field leave the unmapped rows the moment — and only the
     * moment — a mapper claims its path group. Filling in the mapping is adding mappers to the
     * registry; nothing here changes along with it.
     *
     * @param  array<string, mixed>  $results
     */
    public function processResults(Building $building, array $results, EventType $eventType): MappingReport
    {
        $report = new MappingReport($building, $eventType);
        $debug = (bool) config('hoomdossier.services.smarttwin.debug', false);

        foreach ($this->flattener->flatten($results) as $leaf) {
            $mapper = $this->registry->forPath($leaf->pathGroup);

            if (is_null($mapper)) {
                $report->unmapped($leaf);
                $this->logLeaf($debug, $building, $leaf, 'unmapped');

                continue;
            }

            try {
                $result = $mapper->map($building, $leaf, $results);
                $report->add($leaf, $result);
                $this->logLeaf($debug, $building, $leaf, $result->status->value, $result->target);
            } catch (Throwable $e) {
                // One field blowing up may not cost us the other 271. It lands in the report as an
                // error and is logged below, so it surfaces without stopping the run.
                $report->error($leaf, $e);
            }
        }

        $this->logReport($building, $eventType, $report);

        return $report;
    }

    private function logReport(Building $building, EventType $eventType, MappingReport $report): void
    {
        Log::debug('SmartTwin mapping finished', array_merge([
            'building_id' => $building->getKey(),
            'flow'        => $eventType->inputSource()->short,
            'fields'      => count($report->entries()),
        ], $report->counts()));

        // A gap in the mapping is expected and lives in the report; a broken mapping is not, and
        // belongs where we actually watch for problems.
        foreach ($report->entriesNeedingAttention() as $entry) {
            Log::warning('SmartTwin mapping needs attention', [
                'building_id' => $building->getKey(),
                'status'      => $entry->status->value,
                'path'        => $entry->leaf->path,
                'target'      => $entry->target,
                'note'        => $entry->note,
            ]);
        }
    }

    /**
     * Per field logging is loud — a single response holds a few hundred leaves — so it hangs off the
     * SmartTwin debug flag that already exists for the API client. The report is the per field
     * record; this is for when you want it in the log alongside the API traffic.
     */
    private function logLeaf(bool $debug, Building $building, Leaf $leaf, string $status, ?string $target = null): void
    {
        if (! $debug) {
            return;
        }

        Log::debug('SmartTwin mapping field', [
            'building_id' => $building->getKey(),
            'path'        => $leaf->path,
            'value'       => $leaf->displayValue(),
            'status'      => $status,
            'target'      => $target,
        ]);
    }
}
