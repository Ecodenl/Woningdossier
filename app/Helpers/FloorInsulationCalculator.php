<?php

namespace App\Helpers;

use App\Helpers\KeyFigures\FloorInsulation\Temperature;
use App\Models\Building;
use App\Models\ElementValue;
use App\Models\InputSource;
use App\Models\UserEnergyHabit;

class FloorInsulationCalculator
{
    public static function calculateGasSavings(Building $building, InputSource $inputSource, ElementValue $element, UserEnergyHabit $energyHabit, $surface, $measureAdvice)
    {
        $result = 0;

        $kengetalEnergySaving = Temperature::energySavingFigureFloorInsulation($measureAdvice);
        self::debug('Kengetal energebesparing = '.$kengetalEnergySaving);

        if (isset($element->calculate_value) && $element->calculate_value < 3) {
            $result = min(
                $surface * $kengetalEnergySaving,
                RawCalculator::maxGasSavings($building, $inputSource, $energyHabit, $element->element)
            );
            self::debug($result.' = min('.$surface.' * '.$kengetalEnergySaving.', '.RawCalculator::maxGasSavings($building, $inputSource, $energyHabit, $element->element).')');
        } else {
            self::debug('No gas savings..');
        }

        return $result;
    }

    protected static function debug($line)
    {
        // \Log::debug($line);
    }
}
