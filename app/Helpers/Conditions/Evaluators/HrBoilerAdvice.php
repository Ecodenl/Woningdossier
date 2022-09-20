<?php

namespace App\Helpers\Conditions\Evaluators;

use App\Calculations\HighEfficiencyBoiler;
use App\Models\Building;
use App\Models\InputSource;
use App\Models\Service;
use App\Models\ToolQuestion;
use App\Traits\HasDynamicAnswers;
use Illuminate\Support\Collection;

class HrBoilerAdvice implements ShouldEvaluate
{
    use HasDynamicAnswers;

    public static function evaluate(Building $building, InputSource $inputSource, $value = null, ?Collection $answers = null): bool
    {
        // This evaluator checks if the boiler_advice is returned in the calculation. This advice tells the user
        // that they won't receive much efficiency improvement because they already have a high quality HR-boiler

        $service = Service::findByShort('heat-pump')->values()
            ->where(
                'calculate_value',
                ToolQuestion::findByShort('new-boiler-type')->toolQuestionCustomValues()
                    ->whereShort(static::getQuickAnswer('new-boiler-type', $building, $inputSource, $answers))->first()->extra['calculate_value'] ?? null
            )->first();

        $results = HighEfficiencyBoiler::calculate($building, $inputSource,
            [
                'building_services' => [
                    'service_value_id' => optional($service)->id,
                ],
            ],
        );

        return array_key_exists('boiler_advice', $results);
    }
}