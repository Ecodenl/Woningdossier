<?php

namespace App\Observers\ToolQuestionAnswer;

use App\Jobs\ApplyExampleBuildingForChanges;
use App\Models\BuildingType;
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
            $building = $toolQuestionAnswer->building;
            $buildingFeature = $building
                ->buildingFeatures()
                ->forInputSource($toolQuestionAnswer->inputSource)
                ->first();

            $buildingType = $buildingTypes->first();
            Log::debug("Only 1 building type found for category {$buildingTypeCategory->name}, lets set the $buildingType->name as building type.");
            $buildingFeature->update(['building_type_id' => $buildingType->id]);

            // now we will try to apply the example building based on the only available building type, but only if the user has a build_year from pico.
            if (!is_null($buildingFeature->build_year)) {
                ApplyExampleBuildingForChanges::dispatchNow($buildingFeature, ['building_type_id' => $buildingType->id], $toolQuestionAnswer->inputSource);
            }
        }
    }
}