<?php

namespace App\Deprecation;

use App\Models\Service;
use App\Models\ToolQuestion;

/**
 * This class exists purely to perform code that in the long run should be refactored.
 *
 * @class ToolHelper
 */
class ToolHelper
{
    // TODO: Caching / allow hard coded IDs for fixed models
    public static function getServiceValueByCustomValue(string $service, string $toolQuestion, $answer)
    {
        return static::getModelByCustomValue(Service::findByShort($service)->values(), $toolQuestion, $answer);
    }
    
    public static function getModelByCustomValue($query, string $toolQuestion, $answer)
    {
        return $query->where(
            'calculate_value',
            ToolQuestion::findByShort($toolQuestion)->toolQuestionCustomValues()
                ->whereShort($answer)->first()->extra['calculate_value'] ?? null
        )->first();
    }
}