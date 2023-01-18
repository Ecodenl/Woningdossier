<?php

namespace App\Helpers\Models;

use App\Models\Scan;

class CooperationMeasureApplicationHelper
{
    const EXTENSIVE_MEASURE = 'extensive';
    const SMALL_MEASURE = 'small';

    public static function getMeasureTypes(): array
    {
        return [
            static::EXTENSIVE_MEASURE,
            static::SMALL_MEASURE,
        ];
    }

    public static function getTypeForScan(Scan $scan): string
    {
        return $scan->short === 'quick-scan'
            ? static::SMALL_MEASURE
            : static::EXTENSIVE_MEASURE;
    }
}