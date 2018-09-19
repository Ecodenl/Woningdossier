<?php

namespace App\Helpers;

use App\Helpers\KeyFigures\RoofInsulation\Temperature;
use App\Models\Building;
use App\Models\BuildingHeating;
use App\Models\ElementValue;
use App\Models\MeasureApplication;
use App\Models\UserEnergyHabit;
use Carbon\Carbon;

class RoofInsulationCalculator
{
    public static function calculateGasSavings(Building $building, ElementValue $element, UserEnergyHabit $energyHabit, BuildingHeating $heating, $surface, $totalSurface, $measureAdvice)
    {
        if (0 == $totalSurface) {
            return 0;
        }
        $result = 0;
        $building->getBuildingType();

        $kengetalEnergySaving = Temperature::energySavingFigureRoofInsulation($measureAdvice, $heating);
        self::debug('Kengetal energebesparing = '.$kengetalEnergySaving);

        if (isset($element->calculate_value) && $element->calculate_value < 3) {
            $result = min(
                $surface * $kengetalEnergySaving,
                ($surface / $totalSurface) * Calculator::maxGasSavings($building, $energyHabit, $element->element)
            );
            self::debug($result.' = min('.$surface.' * '.$kengetalEnergySaving.', ('.$surface / $totalSurface.') * '.Calculator::maxGasSavings($building, $energyHabit, $element->element).')');
        }

        return $result;
    }

    public static function determineApplicationYear(MeasureApplication $measureApplication, $last, $factor)
    {
        self::debug(__METHOD__);

        $correctedMaintenanceInterval = ceil($factor * $measureApplication->maintenance_interval);

        if ($last + $correctedMaintenanceInterval <= Carbon::now()->year) {
            self::debug('Last replacement is longer than '.$correctedMaintenanceInterval.' years ago.');
            $year = Carbon::now()->year;
        } else {
            $year = $last + $correctedMaintenanceInterval;
        }

        return $year;
    }

    protected static function debug($line)
    {
        \Log::debug($line);
    }
}
