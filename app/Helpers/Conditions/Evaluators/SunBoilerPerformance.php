<?php

namespace App\Helpers\Conditions\Evaluators;

use App\Calculations\Heater;
use App\Models\Building;
use App\Models\ComfortLevelTapWater;
use App\Models\InputSource;
use App\Models\ToolQuestion;
use App\Traits\HasDynamicAnswers;
use Illuminate\Support\Collection;

class SunBoilerPerformance implements ShouldEvaluate
{
    use HasDynamicAnswers;

    public static function evaluate(Building $building, InputSource $inputSource, $value = null, ?Collection $answers = null): bool
    {
        // This evaluator checks the performance for the sun-boiler in the user's situation. The calculation
        // returns a given color which defines the performance.

        $newWaterShort = static::getQuickAnswer('new-water-comfort', $building, $inputSource, $answers);
        $newWaterComfort = ToolQuestion::findByShort('new-water-comfort')->toolQuestionCustomValues()
            ->whereShort($newWaterShort)->first();
        $newWater = ComfortLevelTapWater::where('calculate_value', $newWaterComfort->extra['calculate_value'] ?? null)->first();

        $results = Heater::calculate(
            $building,
            $inputSource,
            [
                'user_energy_habits' => [
                    'water_comfort_id' => optional($newWater)->id,
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