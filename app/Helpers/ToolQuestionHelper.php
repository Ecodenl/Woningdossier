<?php

namespace App\Helpers;

use App\Models\Building;
use App\Models\ToolQuestion;
use Illuminate\Support\Facades\Schema;

class ToolQuestionHelper {

    /**
     * These tables should query on a extra column
     */
    const TABLE_COLUMN = [
        'building_elements' => 'element_id',
        'building_services' => 'service_id',
    ];

    /**
     * Simple method to resolve the save in to something we can use.
     *
     * @param ToolQuestion $toolQuestion
     * @param Building $building
     * @return array
     */
    public static function resolveSaveIn(ToolQuestion $toolQuestion): array
    {
        $savedInParts = explode('.', $toolQuestion->save_in);
        $table = $savedInParts[0];
        $column = $savedInParts[1];

        // 2 parts is the simple scenario, this just means a table + column
        // but in some cases it holds more info we need to build wheres.
        if (count($savedInParts) > 2) {
            // in this case the column holds a extra where value
            $where[] = [ToolQuestionHelper::TABLE_COLUMN[$table], '=', $column];

            $columns = array_slice($savedInParts, 2);
            $column = implode('.', $columns);
        }

        return compact('table', 'column');
    }
}