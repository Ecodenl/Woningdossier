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
            $building->user->energyHabit()->forInputSource($inputSource)->first(),
            [
                'user_energy_habits' => [
                    'water_comfort_id' => static::getQuickAnswer('new-water-comfort', $building, $inputSource, $answers),
                ],
                'building_heaters' => [
                    'pv_panel_orientation_id' => static::getQuickAnswer('heater-pv-panel-orientation', $building, $inputSource, $answers),
                    'angle' => static::getQuickAnswer('heater-pv-panel-angle', $building, $inputSource, $answers),
                ],
                'answers' => $answers,
            ],
        );

        return data_get($results, 'performance.alert') === $value;
    }
}