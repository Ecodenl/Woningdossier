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
}
