<?php

namespace App\Helpers\KeyFigures\WallInsulation;

class Temperature
{
    const WALL_INSULATION_JOINTS = 'Spouwmuurisolatie';
    const WALL_INSULATION_FACADE = 'Binnengevelisolatie';
    const WALL_INSULATION_RESEARCH = 'Nader onderzoek nodig';

    // Gemiddelde temperatuur normberekening
    const AVERAGE_TEMPERATURE_NORM = 13.5; // degrees

    protected static $calculationValues = [
        self::WALL_INSULATION_JOINTS => [
            'default' => 6.46,
            'correction' => 0.83,
        ],
        self::WALL_INSULATION_FACADE => [
            'default' => 7.06,
            'correction' => 0.91,
        ],
        self::WALL_INSULATION_RESEARCH => [
            'default' => 6.46,
            'correction' => 0.83,
        ],
    ];

    /**
     * kengetal energiebesparing.
     *
     * @param string $measure Use WALL_INSULATION_* consts
     * @param $avgHouseTemp
     *
     * @return null|string Null on failure
     */
    public static function energySavingFigureWallInsulation($measure, $avgHouseTemp)
    {
        if (! array_key_exists($measure, self::$calculationValues)) {
            return null;
        }
        if ($avgHouseTemp < self::AVERAGE_TEMPERATURE_NORM) {
        	\Log::debug("Average house temperature is below norm (" . $avgHouseTemp . " vs. " . self::AVERAGE_TEMPERATURE_NORM . "). Using the norm..");
            $avgHouseTemp = self::AVERAGE_TEMPERATURE_NORM;
        }

        $calcValues = self::$calculationValues[$measure];

        return number_format($calcValues['default'] + $calcValues['correction'] * ($avgHouseTemp - self::AVERAGE_TEMPERATURE_NORM), 2);
    }
}
