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
        // Makes it easier to query
        $answers = is_null($answers) ? collect() : $answers;

        $newBoilerQuestion = ToolQuestion::findByShort('new-boiler-type');
        $newBoilerAnswer = $answers->has('new-boiler-type')
            ? $answers->get('new-boiler-type')
            : $building->getAnswer($inputSource, $newBoilerQuestion);
        $newBoilerServiceValue = ServiceValue::find($newBoilerAnswer);

        $newTempQuestion = ToolQuestion::findByShort('new-boiler-setting-comfort-heat');
        $newTempAnswer = $answers->has('new-boiler-setting-comfort-heat')
            ? $answers->get('new-boiler-setting-comfort-heat')
            : $building->getAnswer($inputSource, $newTempQuestion);
        $newTempCustomValue = $newTempQuestion->toolQuestionCustomValues()->whereShort($newTempAnswer)->first();

        if ($newTempCustomValue instanceof ToolQuestionCustomValue) {
            $newHeatPumpQuestion = ToolQuestion::findByShort('new-heat-pump-type');
            $newHeatPumpAnswer = $answers->has('new-heat-pump-type')
                ? $answers->get('new-heat-pump-type')
                : $building->getAnswer($inputSource, $newHeatPumpQuestion);
            $newHeatPumpServiceValue = ServiceValue::find($newHeatPumpAnswer);

            // TODO: Heat pump boiler

            if ($newHeatPumpServiceValue instanceof ServiceValue) {
                $desiredPowerQuestion = ToolQuestion::findByShort('heat-pump-preferred-power');
                $desiredPowerAnswer = $answers->has('heat-pump-preferred-power')
                    ? $answers->get('heat-pump-preferred-power')
                    : $building->getAnswer($inputSource, $desiredPowerQuestion);


                $calculator = new HeatPump(
                    $building, $inputSource,
                    $building->user->energyHabit()->forInputSource($inputSource)->first(),
                    [
                        'boiler' => $newBoilerServiceValue,
                        'heatingTemperature' => $newTempCustomValue,
                        'heatPumpConfigurable' => $newHeatPumpServiceValue,
                        'desiredPower' => $desiredPowerAnswer,
                        'answers' => $answers,
                    ]
                );

                return $calculator->insulationScore() < 2.5;
            }
        }

        // If we don't have the right data, we can't calculate
        return false;
    }
}