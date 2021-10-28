<?php

namespace App\Helpers;

use App\Models\Building;
use App\Models\ToolQuestion;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ToolQuestionHelper {

    /**
     * These tables should query on one or more extra column(s)
     * Order for multiple columns is very important
     */
    const TABLE_COLUMN = [
        'building_elements' => 'element_id',
        'building_services' => 'service_id',
        'step_comments' => [
            'step_id',
            'short',
        ],
    ];

    /**
     * An array map of tool questions that should do a full recalculate on change.
     */
    const TOOL_QUESTION_FULL_RECALCULATE = [
        'building-type',
        'building-type-category',
        'thermostat-high',
        'thermostat-low',
        'hours-high',
        'heating-first-floor',
        'heating-second-floor',
        'surface',
        'resident-count',
        'water-comfort',
        'amount-gas',
        'amount-electricity',
    ];

    /**
     * Array map that will link the tool question short to matching step shorts; eg
     * The tool question "current-floor-insulation" will also be questioned on the expert step "floor-insulation"
     * Thus we will map it to floor insulation step.
     */
    const TOOL_QUESTION_STEP_MAP = [
        'roof-type' => ['roof-insulation'],
        'water-comfort' => ['heater'],
        'cook-type' => ['high-efficiency-boiler'],
        'current-wall-insulation' => ['wall-insulation'],
        'current-floor-insulation' => ['floor-insulation'],
        'current-roof-insulation' => ['roof-insulation'],
        'current-living-rooms-windows' => ['insulated-glazing'],
        'current-sleeping-rooms-windows' => ['insulated-glazing'],
        'heat-source' => ['high-efficiency-boiler'],
        'boiler-type' => ['high-efficiency-boiler'],
        'boiler-placed-date' => ['high-efficiency-boiler'],
        'heater-type' => ['heater'],
        'ventilation-type' => ['ventilation'],
        'ventilation-demand-driven' => ['ventilation'],
        'ventilation-heat-recovery' => ['ventilation'],
        'crack-sealing-type' => ['ventilation'],
        'has-solar-panels' => ['solar-panels'],
        'solar-panel-count' => ['solar-panels'],
        'total-installed-power' => ['solar-panels'],
        'solar-panels-placed-date' => ['solar-panels'],
    ];

    public static function stepShortsForToolQuestion(ToolQuestion $toolQuestion): array
    {
        return self::TOOL_QUESTION_STEP_MAP[$toolQuestion->short];
    }

    /**
     * Simple method to determine whether the given tool question should use the old advice on a recalculate
     *
     */
    public static function shouldToolQuestionDoFullRecalculate(ToolQuestion $toolQuestion): bool
    {
        return in_array($toolQuestion->short,self::TOOL_QUESTION_FULL_RECALCULATE, true);
    }

    /**
     * Simple method to resolve the save in to something we can use.
     *
     * @param ToolQuestion $toolQuestion
     * @param Building $building
     * @return array
     */
    public static function resolveSaveIn(ToolQuestion $toolQuestion, Building $building): array
    {
        $savedInParts = explode('.', $toolQuestion->save_in);
        $table = $savedInParts[0];
        $column = $savedInParts[1];
        $where = [];

        if (Schema::hasColumn($table, 'user_id')) {
            $where[] = ['user_id', '=', $building->user_id];
        } else {
            $where[] = ['building_id', '=', $building->id];
        }

        // 2 parts is the simple scenario, this just means a table + column
        // but in some cases it holds more info we need to build wheres.
        if (count($savedInParts) > 2) {
            // In this case the column holds extra where values

            // There's 2 cases. Either it's a single value, or a set of columns
            if (Str::contains($column, '_')) {
                // Set of columns, we set the wheres based on the order of values
                $columns = ToolQuestionHelper::TABLE_COLUMN[$table];
                $values = explode('_', $column);

                // Currently only for step_comments that can have a short
                foreach ($values as $index => $value) {
                    $where[] = [$columns[$index], '=', $value];
                }
            } else {
                // Just a value, but the short table could be an array. We grab the first
                $columns = ToolQuestionHelper::TABLE_COLUMN[$table];
                $columnForWhere = is_array($columns) ? $columns[0] : $columns;

                $where[] = [$columnForWhere, '=', $column];
            }

            $columns = array_slice($savedInParts, 2);
            $column = implode('.', $columns);
        }

        return compact('table', 'column', 'where');
    }
}