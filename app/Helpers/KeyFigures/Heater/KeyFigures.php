<?php

namespace App\Helpers\KeyFigures\Heater;

use App\Helpers\KeyFigures\KeyFiguresInterface;
use App\Models\ComfortLevelTapWater;
use App\Models\HeaterSpecification;
use App\Models\KeyFigureConsumptionTapWater;
use App\Models\PvPanelLocationFactor;
use App\Models\PvPanelOrientation;
use App\Models\PvPanelYield;
use App\Models\UserEnergyHabit;

class KeyFigures implements KeyFiguresInterface
{
    const M3_GAS_TO_KWH = 8.792; // m3 gas -> kWh

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
     * @param int|null $residentCount
     * @param \App\Models\ComfortLevelTapWater $comfortLevel
     *
     * @return \App\Models\KeyFigureConsumptionTapWater|null
     */
    public static function getCurrentConsumption(?int $residentCount, ComfortLevelTapWater $comfortLevel): ?KeyFigureConsumptionTapWater
    {
        $key = sprintf('heat-pump-characteristics-%s-%s', $residentCount, $comfortLevel->id);
        $ttl = 60 * 60 * 24; // 24 hours
        return cache()->remember($key, $ttl, function () use ($residentCount, $comfortLevel) {
            return KeyFigureConsumptionTapWater::where('resident_count', $residentCount)
                                               ->where('comfort_level_tap_water_id', $comfortLevel->id)
                                               ->first();
        });


    }

    /**
     * @param int   $waterConsumption
     * @param float $helpFactor
     *
     * @return array
     */
    public static function getSystemSpecifications($waterConsumption, $helpFactor)
    {
        $initialHeater = HeaterSpecification::where('liters', $waterConsumption)->first();

        // the water consumption comes from the key_figure_consumption_tap_waters, the max resident is 8 after that there is no waterconsumption thing
        // and if thats empty we can calc anything so return empty array
        if (! $initialHeater instanceof HeaterSpecification) {
            return [];
        }
        $relativeCollectorSize = $initialHeater->collector * (1 / $helpFactor);
        // \Log::debug('Heater: Relative collector size: '.$relativeCollectorSize);

        $advisedSize = self::getAdvisedCollectorSize($relativeCollectorSize);
        // \Log::debug('Heater: Advised collector size: '.$advisedSize);

        return [
            'boiler' => $initialHeater->boiler,
            'collector' => $advisedSize,
            'production_heat' => $initialHeater->savings,
        ];
    }

    /**
     * Return the advised collector size based on the relative collector size.
     *
     * @param float $relativeCollectorSize
     *
     * @return float
     */
    public static function getAdvisedCollectorSize($relativeCollectorSize)
    {
        if ($relativeCollectorSize <= 2) {
            return 1.6;
        }
        if ($relativeCollectorSize < 2.9) {
            return 2.5;
        }
        if ($relativeCollectorSize < 4.1) {
            return 3.2;
        }
        if ($relativeCollectorSize < 5.6) {
            return 4.8;
        }

        return 6.4;
    }

    public static function getAngles()
    {
        return self::$angles;
    }

    /**
     * Returns the key figures from this class.
     *
     * @return array
     */
    public static function getKeyFigures()
    {
        $figures = [];

        $figures['M3_GAS_TO_KWH'] = self::M3_GAS_TO_KWH;

        return $figures;
    }
}
