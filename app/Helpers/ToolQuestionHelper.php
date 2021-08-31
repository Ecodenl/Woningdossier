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