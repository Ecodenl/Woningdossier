<?php

namespace App\Helpers\KeyFigures\PvPanels;

use App\Models\PvPanelLocationFactor;
use App\Models\PvPanelOrientation;
use App\Models\PvPanelYield;

class KeyFigures
{
    const SOLAR_PANEL_ELECTRICITY_COST_FACTOR = 0.92; // unit of measure??
    const SOLAR_PANEL_SURFACE = 1.6; // m2

    const COST_KWH = 0.23; // euro
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
	];

    /**
     * @param $zipcode
     *
     * @return PvPanelLocationFactor|null
     */
    public static function getLocationFactor($zipcode)
    {
        $pc2 = substr($zipcode, 0, 2);

        return PvPanelLocationFactor::where('pc2', $pc2)->first();
    }

    /**
     * @param PvPanelOrientation $orientation
     * @param $angle
     *
     * @return PvPanelYield|null
     */
    public static function getYield(PvPanelOrientation $orientation, $angle)
    {
        return PvPanelYield::where('pv_panel_orientation_id', $orientation->id)
            ->where('angle', $angle)->first();
    }

    public static function getAngles(){
    	return self::$angles;
    }

    public static function getPeakPowers(){
    	return self::$peakPowers;
    }

}
