<?php

namespace App\Helpers\Conditions\Evaluators;

use App\Calculations\HeatPump;
use App\Models\Building;
use App\Models\InputSource;
use App\Models\ServiceValue;
use App\Models\ToolQuestion;
use App\Models\ToolQuestionCustomValue;
use Illuminate\Support\Collection;

class InsulationCalculation implements ShouldEvaluate
{
    public static function evaluate(Building $building, InputSource $inputSource, ?Collection $answers = null): bool
    {
        $calculator = new HeatPump(
            $building, $inputSource,
            $building->user->energyHabit()->forInputSource($inputSource)->first(),
            [],
        );

        return $calculator->insulationScore() < 2.5;
    }
}