<?php

namespace App\Helpers\Conditions\Evaluators;

use App\Calculations\HeatPump;
use App\Models\Building;
use App\Models\InputSource;
use Illuminate\Support\Collection;

class InsulationScore implements ShouldEvaluate
{
    public static function evaluate(Building $building, InputSource $inputSource, $value = null, ?Collection $answers = null): bool
    {
        $calculator = new HeatPump(
            $building, $inputSource,
            $building->user->energyHabit()->forInputSource($inputSource)->first(),
            [],
        );

        return $calculator->insulationScore() < 2.5;
    }
}