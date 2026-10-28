<?php

namespace App\Services\SmartTwin;

use App\Models\Building;
use App\Models\InputSource;
use Illuminate\Support\Facades\DB;

/**
 * Makes an input source's rows about the dwelling equal to master before SmartTwin writes in them,
 * so that what SmartTwin writes lands on top of the dossier as it is, rather than replacing it.
 *
 * Why this is needed. A SmartTwin import, and SmartTwin's Building Data, write the answers in the
 * input source of the flow: the coach after the coach's advice, the resident after the resident's.
 * GetMyValuesTrait then copies the whole row of that input source to master, empty columns
 * included, the moment the row is saved a second time. If that input source does not hold what
 * master holds, master loses it. The commonest case is a coach who never opened the scan in
 * Hoomdossier, as in a cooperation with SmartTwin the coach goes from the woonplan straight to
 * SmartTwin: the coach has no row, the first write creates one with a single column, and the second
 * empties every other column of master's row. Reproduced with a real import on 15 October 2026:
 * the resident's build year, surface, monument, building layers and energy label were gone from
 * master. The resident's own flow loses what only master holds, such as EP-Online's label, which
 * EP-Online writes into master directly.
 *
 * The tool does not have the problem: DuplicateDataForBuilding copies master into the input
 * source of whoever opens the scan, before anything is asked. The imports bypass it. Its
 * CloneDataService does not fit here: it only clears building_features before copying, and
 * appends to every other table, where an input source that already has rows (the coach always has
 * advices once SmartTwin owns the woonplan) would end up with duplicates.
 *
 * So this replaces, for the tables SmartTwin writes about the dwelling and nothing else. Which
 * those are is SmartTwin's mapping's to say; checked against a real import's report: the
 * building's features, its elements and its roof types. Should the mapping start writing another
 * table with a row per input source (installations, say), it belongs in TABLES.
 *
 * The input source's own values that differ from master are replaced by master's. That changes
 * nothing that is shown: master is what the dossier shows, and it is the same before and after.
 *
 * Decision 2026-10-15-smarttwin-import-overschrijft-master (M1).
 */
class DwellingRows
{
    /**
     * The tables SmartTwin writes about the dwelling, each with a row per input source (and per
     * element or roof type). Nothing refers to their rows by id, so they can be replaced outright.
     */
    public const TABLES = ['building_features', 'building_elements', 'building_roof_types'];

    /**
     * Replaces the input source's rows in TABLES with copies of master's.
     *
     * Through the query builder, not the models: a model save would set GetMyValuesTrait copying
     * the row straight back to master, and a model delete of a roof type would delete master's.
     * A table master has no rows in is left alone: there is nothing there to lose, and replacing
     * would only throw away the input source's rows.
     */
    public function equalizeToMaster(Building $building, InputSource $inputSource): void
    {
        $master = InputSource::master();

        if ($inputSource->id === $master->id) {
            return;
        }

        DB::transaction(function () use ($building, $inputSource, $master) {
            foreach (self::TABLES as $table) {
                $masterRows = DB::table($table)
                    ->where('building_id', $building->id)
                    ->where('input_source_id', $master->id)
                    ->get();

                if ($masterRows->isEmpty()) {
                    continue;
                }

                DB::table($table)
                    ->where('building_id', $building->id)
                    ->where('input_source_id', $inputSource->id)
                    ->delete();

                DB::table($table)->insert($masterRows->map(function (object $row) use ($inputSource) {
                    $copy = (array) $row;
                    unset($copy['id']);
                    $copy['input_source_id'] = $inputSource->id;

                    return $copy;
                })->all());
            }
        });
    }
}
