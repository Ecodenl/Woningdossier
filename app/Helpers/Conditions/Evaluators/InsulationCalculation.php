<?php

namespace App\Helpers\Conditions\Evaluators;

use App\Calculations\HeatPump;
use App\Models\Building;
use App\Models\InputSource;
use App\Models\ServiceValue;
use App\Models\ToolQuestion;
use App\Models\BuildingType as BuildingTypeModel;
use App\Models\ToolQuestionCustomValue;
use Illuminate\Support\Collection;

class InsulationCalculation implements ShouldEvaluate
{
    public static function evaluate(Building $building, InputSource $inputSource, ?Collection $answers = null): bool
    {
        $newBoilerQuestion = ToolQuestion::findByShort('new-boiler-type');
        $newBoilerServiceValue = ServiceValue::find($building->getAnswer($inputSource, $newBoilerQuestion));

        $newTempQuestion = ToolQuestion::findByShort('new-boiler-setting-comfort-heat');
        $newTempAnswer = $building->getAnswer($inputSource, $newTempQuestion);
        $newTempCustomValue = $newTempQuestion->toolQuestionCustomValues()->whereShort($newTempAnswer)->first();

        if ($newTempCustomValue instanceof ToolQuestionCustomValue) {
            $newHeatPumpQuestion = ToolQuestion::findByShort('new-heat-pump-type');
            $newHeatPumpAnswer = $building->getAnswer($inputSource, $newHeatPumpQuestion);
            $newHeatPumpCustomValue = $newHeatPumpQuestion->toolQuestionCustomValues()->whereShort($newHeatPumpAnswer)->first();

            // TODO: Heat pump boiler

            if ($newHeatPumpCustomValue instanceof ToolQuestionCustomValue) {
                $desiredPowerQuestion = ToolQuestion::findByShort('heat-pump-preferred-power');
                $desiredPowerAnswer = $building->getAnswer($inputSource, $desiredPowerQuestion);

                $calculator = new HeatPump(
                    $building, $inputSource,
                    $building->user->energyHabit()->forInputSource($inputSource)->first(),
                    [
                        'oiler' => $newBoilerServiceValue,
                        'heatingTemperature' => $newTempCustomValue,
                        'heatPumpConfigurable' => $newHeatPumpCustomValue,
                        'desiredPower' => $desiredPowerAnswer,
                    ]
                );

                return $calculator->insulationScore() < 2.5;
            }
        }

        // If we don't have the right data, we can't calculate
        return false;
    }
}