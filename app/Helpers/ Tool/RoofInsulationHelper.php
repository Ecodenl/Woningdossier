<?php

namespace App\Helpers\Cooperation\Tool;

use App\Events\StepCleared;
use App\Models\Building;
use App\Models\BuildingElement;
use App\Models\BuildingFeature;
use App\Models\BuildingRoofType;
use App\Models\Element;
use App\Models\InputSource;
use App\Models\Step;
use App\Scopes\GetValueScope;
use App\Services\ModelService;

class RoofInsulationHelper
{

    /**
     * Method to clear all the saved data for the step, except for the comments.
     *
     * @param Building $building
     * @param InputSource $inputSource
     * @param array $buildingFeatureData
     * @param array $buildingElementData
     */
    public static function save(Building $building, InputSource $inputSource, array $buildingFeatureData, array $buildingRoofTypeData)
    {
        BuildingFeature::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
            [
                'building_id' => $building->id,
                'input_source_id' => $inputSource->id,
            ],
            $buildingFeatureData
        );

        // we dont know which roof_type_id we will get, so we delete all the rows and create new ones.
        ModelService::deleteAndCreate(BuildingRoofType::class,
            [
                'building_id' => $building->id,
                'input_source_id' => $inputSource->id,
            ],
            $buildingRoofTypeData
        );
    }

    /**
     * Method to clear the building feature data for wall insulation step.
     *
     * @param Building $building
     * @param InputSource $inputSource
     */
    public static function clear(Building $building, InputSource $inputSource)
    {
        BuildingFeature::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
            [
                'building_id' => $building->id,
                'input_source_id' => $inputSource->id,
            ],
            [
                'roof_type_id' => null
            ]
        );

        // delete my own building roof types.
        BuildingRoofType::forMe($building->user)->forInputSource($inputSource)->delete();

        StepCleared::dispatch($building->user, $inputSource, Step::findByShort('roof-insulation'));
    }
}