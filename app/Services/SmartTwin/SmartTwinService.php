<?php

namespace App\Services\SmartTwin;

use App\Enums\SmartTwin\EventType;
use App\Jobs\SmartTwin\Out\GetBuildingData;
use App\Models\Building;
use App\Models\InputSource;
use App\Services\SmartTwin\Mapping\Leaf;
use App\Services\SmartTwin\Mapping\MapperRegistry;
use App\Services\SmartTwin\Mapping\MappingApplier;
use App\Services\SmartTwin\Mapping\MappingReport;
use App\Services\SmartTwin\Mapping\ResponseFlattener;
use Illuminate\Support\Facades\Log;
use Throwable;

class SmartTwinService
{
    public function __construct(
        private readonly ResponseFlattener $flattener,
        private readonly MapperRegistry $registry,
        private readonly MappingApplier $applier,
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
    public function processResults(
        Building $building,
        array $results,
        EventType $eventType,
        ?string $dossierId = null,
    ): MappingReport
    {
        $report = new MappingReport($building, $eventType, $dossierId);
        $debug = (bool) config('hoomdossier.services.smarttwin.debug', false);
        // One response is a few hundred leaves; the flow's input source is the same for all of them.
        $inputSource = $eventType->inputSource();

        [$dwelling, $solutions] = $this->dwellingAndSolutions($this->flattener->flatten($results));

        // The dwelling first, then the action plan in one go: the applier clears it right before
        // the first advice it writes, and refills it with the package.
        $this->applier->startRun();
        $this->walk($dwelling, $building, $inputSource, $results, $report, $debug);
        $this->walk($solutions, $building, $inputSource, $results, $report, $debug);

        // From the first SmartTwin advice on, the woonplan is SmartTwin's advice plus what the
        // resident chose, with "In orde" cards for what is in good shape and not advised. Only
        // then, though: a response that advised nothing leaves the plan, and the package it was
        // calculated for, as they were.
        if ($report->savedAdvice()) {
            $this->applier->addInOrderCards($building, $inputSource);
            $this->applier->recordPackage($building, $inputSource, $results);

            // The advice says nothing about the building type, build year, surface or registered
            // label; SmartTwin's Building Data does, and is fetched again with every advice.
            GetBuildingData::dispatchFor($building, $inputSource);
        }

        $this->logReport($building, $eventType, $report);

        return $report;
    }

    /**
     * @param  array<int, Leaf>  $leaves
     * @param  array<string, mixed>  $results
     */
    private function walk(
        array $leaves,
        Building $building,
        InputSource $inputSource,
        array $results,
        MappingReport $report,
        bool $debug,
    ): void
    {
        foreach ($leaves as $leaf) {
            $mapper = $this->registry->forPath($leaf->pathGroup);

            if (is_null($mapper)) {
                $report->unmapped($leaf);
                $this->logLeaf($debug, $building, $leaf, 'unmapped');

                continue;
            }

            try {
                // Deciding and writing are one step from here: the mapper says where the value
                // belongs, the applier puts it there and hands back the result as it should be
                // reported — a mapping pointing at a short that does not exist comes back as
                // TARGET_MISSING, so the report describes what happened rather than what was meant.
                $decided = $mapper->map($building, $leaf, $results);

                foreach (is_array($decided) ? $decided : [$decided] as $result) {
                    $result = $this->applier->apply($building, $inputSource, $result);

                    $report->add($leaf, $result);
                    $this->logLeaf($debug, $building, $leaf, $result->status->value, $result->target);
                }
            } catch (Throwable $e) {
                // One field blowing up may not cost us the other 271. It lands in the report as an
                // error and is logged below, so it surfaces without stopping the run. A field that
                // threw while writing is not reported as mapped: the entry is an error instead.
                $report->error($leaf, $e);
            }
        }
    }

    /**
     * The leaves about the dwelling, and those of the solutions.
     *
     * The action plan is cleared right before the solutions are written, so they have to come
     * together and last. The response happens to list them last; splitting here makes that a rule
     * instead of a coincidence of how the JSON was serialised.
     *
     * @param  array<int, Leaf>  $leaves
     * @return array{0: array<int, Leaf>, 1: array<int, Leaf>}
     */
    private function dwellingAndSolutions(array $leaves): array
    {
        $isSolution = fn (Leaf $leaf) => str_starts_with($leaf->path, 'scenario.solutions.');

        return [
            array_values(array_filter($leaves, fn (Leaf $leaf) => ! $isSolution($leaf))),
            array_values(array_filter($leaves, $isSolution)),
        ];
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
