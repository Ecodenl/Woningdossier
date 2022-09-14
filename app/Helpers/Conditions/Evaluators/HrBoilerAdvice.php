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
        $service = Service::findByShort('heat-pump')->values()
            ->where(
                'calculate_value',
                ToolQuestion::findByShort('new-boiler-type')->toolQuestionCustomValues()
                    ->whereShort(static::getQuickAnswer('new-boiler-type', $building, $inputSource, $answers))->first()->extra['calculate_value'] ?? null
            )->first();

        $results = HighEfficiencyBoiler::calculate(
            $building->user->energyHabit()->forInputSource($inputSource)->first(),
            [
                'building_services' => [
                    'service_value_id' => optional($service)->id,
                ],
            ],
        );

        return array_key_exists('boiler_advice', $results);
    }
}