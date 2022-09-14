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
        $newWaterShort = static::getQuickAnswer('new-water-comfort', $building, $inputSource, $answers);
        $value = ToolQuestion::findByShort('new-water-comfort')->toolQuestionCustomValues()
            ->whereShort($newWaterShort)->first();
        $newWater = ComfortLevelTapWater::where('calculate_value', $value->extra['calculate_value'] ?? null)->first();

        $results = Heater::calculate(
            $building,
            $building->user->energyHabit()->forInputSource($inputSource)->first(),
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