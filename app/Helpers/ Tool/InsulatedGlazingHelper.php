<?php

namespace App\Helpers\Cooperation\Tool;

use App\Models\Building;
use App\Models\BuildingElement;
use App\Models\BuildingFeature;
use App\Models\BuildingInsulatedGlazing;
use App\Models\Element;
use App\Models\ElementValue;
use App\Models\InputSource;
use App\Scopes\GetValueScope;
use App\Services\ModelService;

class InsulatedGlazingHelper
{

    /**
     * Method to clear all the saved data for the step, except for the comments.
     *
     * @param Building $building
     * @param InputSource $inputSource
     * @param array $buildingFeatureData
     * @param array $buildingElementData
     */
    public static function save(Building $building, InputSource $inputSource, array $buildingFeatureData, array $buildingInsulatedGlazingData, array $buildingElementData)
    {
        foreach ($buildingInsulatedGlazingData as $measureApplicationId => $buildingInsulatedGlazing) {
            // update or Create the buildingInsulatedGlazing
            BuildingInsulatedGlazing::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
                [
                    'building_id' => $building->id,
                    'input_source_id' => $inputSource->id,
                    'measure_application_id' => $measureApplicationId,
                ],
                $buildingInsulatedGlazingData
            );
        }


        $frames = Element::where('short', 'frames')->first();
        $woodElements = Element::where('short', 'wood-elements')->first();

        $buildingElementData[$frames->id];
        BuildingElement::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
            [
                'building_id' => $building->id,
                'input_source_id' => $inputSource->id,
                'element_id' => $element->id,
            ],
            [
                'element_value_id' => $elementValue->id,
            ]
        );


//        // saving the main building elements
//        $elements = $request->input('building_elements', []);
//        foreach ($elements as $elementId => $elementValueId) {
//            $element = Element::find($elementId);
//            $elementValue = ElementValue::find(reset($elementValueId));
//
//            if ($element instanceof Element && $elementValue instanceof ElementValue) {
//                BuildingElement::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
//                    [
//                        'element_id' => $element->id,
//                        'input_source_id' => $inputSourceId,
//                        'building_id' => $buildingId,
//                    ],
//                    [
//                        'element_value_id' => $elementValue->id,
//                    ]
//                );
//            }
//        }
//
//        $woodElements = $request->input('building_elements.wood-elements', []);
//
//        $woodElementCreateData = [];
//        foreach ($woodElements as $woodElementId => $woodElementValueIds) {
//            // add the data we need to perform a create
//            foreach ($woodElementValueIds as $woodElementValueId) {
//                array_push($woodElementCreateData, ['element_value_id' => $woodElementValueId]);
//            }
//
//            ModelService::deleteAndCreate(BuildingElement::class,
//                [
//                    'building_id' => $buildingId,
//                    'element_id' => $woodElementId,
//                    'input_source_id' => $inputSourceId,
//                ],
//                $woodElementCreateData
//            );
//        }

BuildingFeature::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
['building_id' => $buildingId,
'input_source_id' => $inputSourceId,],
$buildingFeatureData
);
}

/**
 * Method to clear the building feature data for wall insulation step.
 *
 * @param Building $building
 * @param InputSource $inputSource
 */
public
static function clear(Building $building, InputSource $inputSource)
{
    BuildingFeature::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
        [
            'building_id' => $building->id,
            'input_source_id' => $inputSource->id,
        ],
        [
            'window_surface' => null
        ]
    );
}
}