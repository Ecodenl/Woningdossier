<?php

namespace App\Helpers\KeyFigures\PvPanels;

use App\Helpers\KeyFigures\KeyFiguresInterface;
use App\Models\PvPanelLocationFactor;
use App\Models\PvPanelOrientation;
use App\Models\PvPanelYield;

class KeyFigures implements KeyFiguresInterface
{
    const COST_WP = 1.50; // euro

    protected static $angles = [
        10 => 10,
        15 => 15,
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

    protected static $peakPowers = [
        260 => 260,
        265 => 265,
        270 => 270,
        275 => 275,
        280 => 280,
        285 => 285,
        290 => 290,
        295 => 295,
        300 => 300,
        330 => 330,
        340 => 340,
        350 => 350,
        360 => 360,
        370 => 370,
        380 => 380,
        390 => 390,
        400 => 400,
        410 => 410,
        420 => 420,
        430 => 430,
        440 => 440,
        450 => 450,
    ];

    public static function getLocationFactor(string $zipcode, string $country): ?PvPanelLocationFactor
    {
        $pc2 = substr($zipcode, 0, 2);

        return PvPanelLocationFactor::where('pc2', $pc2)
            ->where('country', $country)
            ->first();
    }

    /**
     * @param $angle
     *
     * @return PvPanelYield|null
     */
    public static function getYield(PvPanelOrientation $orientation, $angle)
    {
        return PvPanelYield::where('pv_panel_orientation_id', $orientation->id)
            ->where('angle', $angle)->first();
    }

    public static function getAngles()
    {
        return self::$angles;
    }

    public static function getPeakPowers()
    {
        return self::$peakPowers;
    }

    /**
     * Returns the key figures from this class.
     *
     * @return array
     */
    public static function getKeyFigures()
    {
        return [
            'COST_WP' => self::COST_WP,
        ];
    }
}
