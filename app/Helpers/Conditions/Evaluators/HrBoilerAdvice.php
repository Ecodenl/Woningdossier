<?php

namespace App\Helpers\Conditions\Evaluators;

use App\Calculations\HighEfficiencyBoiler;
use App\Models\Building;
use App\Models\InputSource;
use App\Traits\HasDynamicAnswers;
use Illuminate\Support\Collection;

class HrBoilerAdvice implements ShouldEvaluate
{
    use HasDynamicAnswers;

    public static function evaluate(Building $building, InputSource $inputSource, $value = null, ?Collection $answers = null): bool
    {
        $results = HighEfficiencyBoiler::calculate(
            $building->user->energyHabit()->forInputSource($inputSource)->first(),
            [
                'building_services' => [
                    'service_value_id' => static::getQuickAnswer('new-boiler-type', $building, $inputSource, $answers),
                ],
            ],
        );

        return array_key_exists('boiler_advice', $results);
    }
}