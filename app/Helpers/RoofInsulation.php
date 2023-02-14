<?php

namespace App\Helpers;

use App\Helpers\KeyFigures\RoofInsulation\Temperature;
use App\Models\MeasureApplication;
use App\Models\RoofType;

class RoofInsulation
{
    public static function getRoofTypeCategory(RoofType $roofType)
    {
        // With the new roof types, we cannot trust the calculate value anymore
        // TODO: When roof types get updated, this might need to be changed again
        $short = RoofType::PRIMARY_TO_SECONDARY_MAP[$roofType->short];
        return $short === 'none' ? '' : $short;
    }

    public static function getRoofTypeSubCategory(RoofType $roofType)
    {
        // TODO: As above, also check if this is correct
        if ($roofType->short === 'pitched') {
            return 'tiles';
        }
        if ($roofType->short === 'flat') {
            return 'bitumen';
        }
//        if (4 == $roofType->calculate_value) {
//            return 'zinc';
//        }

        return '';
    }

    public static function getMeasureApplicationsAdviceMap()
    {
        return [
            'flat' => [
                Temperature::ROOF_INSULATION_FLAT_ON_CURRENT => MeasureApplication::findByShort('roof-insulation-flat-current'),
                Temperature::ROOF_INSULATION_FLAT_REPLACE => MeasureApplication::findByShort('roof-insulation-flat-replace-current'),
            ],
            'pitched' => [
                Temperature::ROOF_INSULATION_PITCHED_INSIDE => MeasureApplication::findByShort('roof-insulation-pitched-inside'),
                Temperature::ROOF_INSULATION_PITCHED_REPLACE_TILES => MeasureApplication::findByShort('roof-insulation-pitched-replace-tiles'),
            ],
        ];
    }
}
