<?php

namespace App\Helpers;

use App\Helpers\KeyFigures\RoofInsulation\Temperature;
use App\Models\MeasureApplication;
use App\Models\RoofType;

class RoofInsulation {

    public static function getRoofTypeCategory(RoofType $roofType)
    {
        if ($roofType->calculate_value <= 2) {
            return 'pitched';
        }
        if ($roofType->calculate_value <= 4) {
            return 'flat';
        }

        return '';
    }

    public static function getRoofTypeSubCategory(RoofType $roofType)
    {
        if (1 == $roofType->calculate_value) {
            return 'tiles';
        }
        if (2 == $roofType->calculate_value) {
            return 'bitumen';
        }
        if (4 == $roofType->calculate_value) {
            return 'zinc';
        }

        return '';
    }

    public static function getMeasureApplicationsAdviceMap()
    {
        return [
            'flat' => [
                Temperature::ROOF_INSULATION_FLAT_ON_CURRENT => MeasureApplication::where('short', 'roof-insulation-flat-current')->first(),
                Temperature::ROOF_INSULATION_FLAT_REPLACE => MeasureApplication::where('short', 'roof-insulation-flat-replace-current')->first(),
            ],
            'pitched' => [
                Temperature::ROOF_INSULATION_PITCHED_INSIDE => MeasureApplication::where('short', 'roof-insulation-pitched-inside')->first(),
                Temperature::ROOF_INSULATION_PITCHED_REPLACE_TILES => MeasureApplication::where('short', 'roof-insulation-pitched-replace-tiles')->first(),
            ],
        ];
    }
}