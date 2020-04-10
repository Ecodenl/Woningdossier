<?php

namespace App\Helpers\Cooperation\Tool;

use App\Events\StepCleared;
use App\Models\Building;
use App\Models\BuildingElement;
use App\Models\BuildingFeature;
use App\Models\Element;
use App\Models\InputSource;
use App\Models\Step;
use App\Scopes\GetValueScope;

class FloorInsulationHelper
{

    /**
     * Method to clear all the saved data for the step, except for the comments.
     *
     * @param Building $building
     * @param InputSource $inputSource
     * @param array $buildingFeatureData
     * @param array $buildingElementData
     */
    public static function save(Building $building, InputSource $inputSource, array $saveData)
    {
        $floorInsulationElement = Element::findByShort('floor-insulation');

        BuildingElement::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
            [
                'building_id' => $building->id,
                'input_source_id' => $inputSource->id,
                'element_id' => $floorInsulationElement->id,
            ],
            [
                'element_value_id' => $saveData['element'][$floorInsulationElement->id]
            ]
        );

        BuildingFeature::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
            [
                'building_id' => $building->id,
                'input_source_id' => $inputSource->id,
            ],
            $saveData['building_features']
        );

        $crawlspaceElement = Element::findByShort('crawlspace');
        BuildingElement::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
            [
                'building_id' => $building->id,
                'input_source_id' => $inputSource->id,
                'element_id' => $crawlspaceElement->id,
            ],
            $saveData['building_elements']
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
                'floor_surface' => null,
                'insulation_surface' => null
            ]
        );

        StepCleared::dispatch($building->user, $inputSource, Step::findByShort('floor-insulation'));
    }
}