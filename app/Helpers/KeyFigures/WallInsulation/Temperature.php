<?php

namespace App\Helpers\KeyFigures\WallInsulation;

use App\Helpers\KeyFigures\KeyFiguresInterface;

class Temperature implements KeyFiguresInterface
{
    const WALL_INSULATION_JOINTS = 'cavity-wall-insulation';
    const WALL_INSULATION_FACADE = 'facade-wall-insulation';
    const WALL_INSULATION_RESEARCH = 'wall-insulation-research';

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
     * @return string|null Null on failure
     */
    public static function energySavingFigureWallInsulation($measure, $avgHouseTemp)
    {
        if (! array_key_exists($measure, self::$calculationValues)) {
            return null;
        }

        $calcValues = self::$calculationValues[$measure];

        return number_format($calcValues['default'] + $calcValues['correction'] * ($avgHouseTemp - self::AVERAGE_TEMPERATURE_NORM), 2);
    }

    /**
     * Returns the key figures from this class.
     *
     * @return array
     */
    public static function getKeyFigures()
    {
        return [
            'AVERAGE_TEMPERATURE_NORM' => self::AVERAGE_TEMPERATURE_NORM,
            'WALL_INSULATION_JOINTS_DEFAULT' => self::$calculationValues[self::WALL_INSULATION_JOINTS]['default'],
            'WALL_INSULATION_JOINTS_CORRECTION' => self::$calculationValues[self::WALL_INSULATION_JOINTS]['correction'],
            'WALL_INSULATION_FACADE_DEFAULT' => self::$calculationValues[self::WALL_INSULATION_FACADE]['default'],
            'WALL_INSULATION_FACADE_CORRECTION' => self::$calculationValues[self::WALL_INSULATION_FACADE]['correction'],
            'WALL_INSULATION_RESEARCH_DEFAULT' => self::$calculationValues[self::WALL_INSULATION_RESEARCH]['default'],
            'WALL_INSULATION_RESEARCH_CORRECTION' => self::$calculationValues[self::WALL_INSULATION_RESEARCH]['correction'],
        ];
    }
}
