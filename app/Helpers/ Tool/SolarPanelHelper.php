<?php

namespace App\Helpers\Cooperation\Tool;

use App\Models\Building;
use App\Models\BuildingElement;
use App\Models\BuildingFeature;
use App\Models\BuildingPvPanel;
use App\Models\Element;
use App\Models\InputSource;
use App\Models\UserEnergyHabit;
use App\Scopes\GetValueScope;

class SolarPanelHelper
{

    /**
     * Method to save the data from the solar panel step
     *
     * @param Building $building
     * @param InputSource $inputSource
     * @param array $buildingFeatureData
     * @param array $buildingElementData
     */
    public static function save(Building $building, InputSource $inputSource, array $saveData)
    {
        BuildingPvPanel::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
            [
                'building_id' => $building->id,
                'input_source_id' => $inputSource->id,
            ],
            $saveData['building_pv_panels']
        );

        UserEnergyHabit::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
            [
                'user_id' => $building->user_id,
                'input_source_id' => $inputSource->id,
            ],
            $saveData['user_energy_habits']
        );
    }

    /**
     * Method to clear the pv panels
     *
     * @param Building $building
     * @param InputSource $inputSource
     */
    public static function clear(Building $building, InputSource $inputSource)
    {
        BuildingPvPanel::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
            [
                'building_id' => $building->id,
                'input_source_id' => $inputSource->id,
            ],
            [
                'peak_power' => null,
                'number' => null,
                'pv_panel_orientation_id' => null,
                'angle' => null,
            ]
        );
    }
}