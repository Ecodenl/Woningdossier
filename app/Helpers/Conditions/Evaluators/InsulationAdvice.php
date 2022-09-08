<?php

namespace App\Helpers\Conditions\Evaluators;

use App\Calculations\HeatPump;
use App\Models\Building;
use App\Models\InputSource;
use Illuminate\Support\Collection;

class InsulationAdvice implements ShouldEvaluate
{
    public static function evaluate(Building $building, InputSource $inputSource, $value = null, ?Collection $answers = null): bool
    {
        $calculator = new HeatPump(
            $building, $inputSource,
            $building->user->energyHabit()->forInputSource($inputSource)->first(),
            [],
        );

        // We don't need the return value, but the method sets the advices
        $calculator->insulationScore();

        return in_array($value, $calculator->getAdvices());
    }
}