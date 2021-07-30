<?php

namespace App\Helpers;

class ToolQuestionHelper {

    /**
     * These tables should query on a extra column
     */
    const TABLE_COLUMN = [
        'building_elements' => 'element_id',
        'building_services' => 'service_id',
    ];
}