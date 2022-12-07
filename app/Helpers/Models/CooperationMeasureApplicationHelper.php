<?php

namespace App\Helpers\Models;

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
}