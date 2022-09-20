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
        // This evaluator checks if the boiler_advice is returned in the calculation. This advice tells the user
        // that they won't receive much efficiency improvement because they already have a high quality HR-boiler

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