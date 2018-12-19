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

	protected static $angles = [
		20 => 20,
		30 => 30,
		40 => 40,
		45 => 45,
		50 => 50,
		60 => 60,
		70 => 70,
		75 => 75,
		90 => 90,
	];

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
     * Get the key figure for the current water consumption.
     *
     * @param UserEnergyHabit $habit
     * @param ComfortLevelTapWater $comfortLevel
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
     * @param float $helpFactor
     *
     * @return array
     */
    public static function getSystemSpecifications($waterConsumption, $helpFactor)
    {
        $initialHeater = HeaterSpecification::where('liters', $waterConsumption)->first();

	    $relativeCollectorSize = $initialHeater->collector * (1/$helpFactor);
	    \Log::debug("Heater: Relative collector size: " . $relativeCollectorSize);

	    $advisedSize = self::getAdvisedCollectorSize($relativeCollectorSize);
	    \Log::debug("Heater: Advised collector size: " . $advisedSize);

	    return [
	    	'boiler' => $initialHeater->boiler,
		    'collector' => $advisedSize,
		    'production_heat' => $initialHeater->savings,
	    ];
    }

	/**
	 * Return the advised collector size based on the relative collector size
	 * @param float $relativeCollectorSize
	 *
	 * @return float
	 */
    public static function getAdvisedCollectorSize($relativeCollectorSize){
		if ($relativeCollectorSize <= 2){
			return 1.6;
		}
		if ($relativeCollectorSize < 2.9){
			return 2.5;
		}
		if ($relativeCollectorSize < 4.1){
			return 3.2;
		}
		if ($relativeCollectorSize < 5.6){
			return 4.8;
		}
		return 6.4;
    }

	public static function getAngles(){
		return self::$angles;
	}
}
