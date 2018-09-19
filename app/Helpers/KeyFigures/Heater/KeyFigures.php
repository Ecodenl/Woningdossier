<?php

namespace App\Helpers\KeyFigures\Heater;

use App\Models\ComfortLevelTapWater;
use App\Models\HeaterSpecification;
use App\Models\KeyFigureConsumptionTapWater;
use App\Models\PvPanelLocationFactor;
use App\Models\PvPanelOrientation;
use App\Models\PvPanelYield;
use App\Models\UserEnergyHabit;

class KeyFigures
{
    /**
     * @param $zipcode
     *
     * @return PvPanelLocationFactor|null
     */
    public static function getLocationFactor($zipcode)
    {
        return \App\Helpers\KeyFigures\PvPanels\KeyFigures::getLocationFactor($zipcode);
    }

    /**
     * @param PvPanelOrientation $orientation
     * @param $angle
     *
     * @return PvPanelYield|null
     */
    public static function getYield(PvPanelOrientation $orientation, $angle)
    {
        return \App\Helpers\KeyFigures\PvPanels\KeyFigures::getYield($orientation, $angle);
    }

    /**
     * @param UserEnergyHabit $habit
     *
     * @return KeyFigureConsumptionTapWater|null
     */
    public static function getCurrentConsumption(UserEnergyHabit $habit, ComfortLevelTapWater $comfortLevel)
    {
        return KeyFigureConsumptionTapWater::where('resident_count', $habit->resident_count)
                                           ->where('comfort_level_tap_water_id', $comfortLevel->id)
                                           ->first();
    }

    /**
     * @param int $waterConsumption
     *
     * @return HeaterSpecification|null
     */
    public static function getSystemSpecifications($waterConsumption)
    {
        return HeaterSpecification::where('liters', $waterConsumption)->first();
    }
}
