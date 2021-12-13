<?php

namespace App\Observers\ToolQuestionAnswer;

use App\Jobs\ApplyExampleBuildingForChanges;
use App\Models\Building;
use App\Models\BuildingFeature;
use App\Models\BuildingType;
use App\Models\InputSource;
use App\Models\ToolQuestionAnswer;
use Illuminate\Support\Facades\Log;

class BuildingTypeCategory implements ShouldApply
{
    public static function apply(ToolQuestionAnswer $toolQuestionAnswer)
    {
        // check if the building type category has multiple building types..
        $buildingTypes = BuildingType::where('building_type_category_id', $toolQuestionAnswer->answer)->get();

        // when there is only one building type, we have to save that for the building
        if ($buildingTypes->count() <= 1) {
            $buildingTypeCategory = \App\Models\BuildingTypeCategory::find($toolQuestionAnswer->answer);
            /** @var Building $building */
            $building = $toolQuestionAnswer->building;

            $buildingFeatureForMasterInputSource = $building
                ->buildingFeatures()
                ->forInputSource(InputSource::findByShort(InputSource::MASTER_SHORT))
                ->first();

            $buildingType = $buildingTypes->first();
            Log::debug("Only 1 building type found for category {$buildingTypeCategory->name}, lets set the $buildingType->name as building type.");

            // check if the current input source has an building feature.
            $buildingFeatureForCurrentInputSource = $building->buildingFeatures()->forInputSource($toolQuestionAnswer->inputSource)->first();


            if ($buildingFeatureForCurrentInputSource instanceof BuildingFeature) {
                $buildingFeatureForCurrentInputSource->update(['building_type_id' => $buildingType->id]);
            } else {
                // if the current input source has no building feature
                // for example; this can happen if the coach starts filling for the resident

                // for this, we replicate the $buildingFeatureForMasterInputSource
                // for the current input source so it has all the building features
                // from the master (build year, surfaces etc.)
                if ($buildingFeatureForMasterInputSource instanceof BuildingFeature){
                    $buildingFeatureForCurrentInputSource = $buildingFeatureForMasterInputSource->replicate(['id']);
                    $buildingFeatureForCurrentInputSource->inputSource()
                                                        ->associate($toolQuestionAnswer->inputSource)
                                                        ->save();
                }
            }

            // now we will try to apply the example building based on the only available building type, but only if the user has a build_year from pico.
            if (!is_null($buildingFeatureForMasterInputSource->build_year)) {
                ApplyExampleBuildingForChanges::dispatchNow($buildingFeatureForMasterInputSource, ['building_type_id' => $buildingType->id], $toolQuestionAnswer->inputSource);
            }
        }
    }
}