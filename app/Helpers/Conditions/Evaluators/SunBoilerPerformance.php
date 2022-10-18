<?php

namespace App\Helpers\Conditions\Evaluators;

use App\Calculations\Heater;
use App\Models\Building;
use App\Models\InputSource;
use App\Traits\HasDynamicAnswers;
use Illuminate\Support\Collection;

class SunBoilerPerformance implements ShouldEvaluate
{
    use HasDynamicAnswers;

    public static function evaluate(Building $building, InputSource $inputSource, $value = null, ?Collection $answers = null): bool
    {
        // This evaluator checks the performance for the sun-boiler in the user's situation. The calculation
        // returns a given color which defines the performance.

        $results = Heater::calculate(
            $building,
            $inputSource,
            $answers,
        );

        return data_get($results, 'performance.alert') === $value;
    }
}