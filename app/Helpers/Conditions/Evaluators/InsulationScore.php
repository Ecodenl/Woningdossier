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
        // This evaluator checks if the user's insulation is good enough to install a heat pump.
        // $value is expected as float/int.

        return HeatPump::init($building, $inputSource, $answers)->insulationScore() < $value;
    }
}