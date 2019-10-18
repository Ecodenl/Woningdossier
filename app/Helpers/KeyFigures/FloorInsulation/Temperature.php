<?php

namespace App\Helpers\KeyFigures\FloorInsulation;

use App\Helpers\KeyFigures\KeyFiguresInterface;

class Temperature implements KeyFiguresInterface
{
    const FLOOR_INSULATION_FLOOR = 'floor-insulation';
    const FLOOR_INSULATION_BOTTOM = 'bottom-insulation';
    const FLOOR_INSULATION_RESEARCH = 'floor-insulation-research';

    protected static $calculationValues = [
        self::FLOOR_INSULATION_FLOOR => 4.04, // D27
        self::FLOOR_INSULATION_BOTTOM => 3.51, // D28
        self::FLOOR_INSULATION_RESEARCH => 3.51, // D29 = D28
    ];

    /**
     * kengetal energiebesparing.
     *
     * @param string $measure Use WALL_INSULATION_* consts
     * @param $avgHouseTemp
     *
     * @return string|null Null on failure
     */
    public static function energySavingFigureFloorInsulation($measure)
    {
        if (! array_key_exists($measure, self::$calculationValues)) {
            return null;
        }

        return number_format(self::$calculationValues[$measure], 2);
    }

    /**
     * Returns the key figures from this class.
     *
     * @return array
     */
    public static function getKeyFigures()
    {
        $figures = [];

        $consts = [
            'FLOOR_INSULATION_FLOOR',
            'FLOOR_INSULATION_BOTTOM',
            'FLOOR_INSULATION_RESEARCH',
        ];

        foreach ($consts as $const) {
            $figures[$const] = self::$calculationValues[constant("self::$const")];
        }

        return $figures;
    }
}
