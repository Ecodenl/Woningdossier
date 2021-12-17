<?php

namespace App\Helpers\QuestionAnswers;

use App\Models\BuildingType;
use App\Models\ToolQuestion;
use Illuminate\Support\Facades\Log;

class BuildingTypeCategory implements ShouldApply
{
    public static function apply(ToolQuestion $toolQuestion, $answer): array
    {
        // check if the building type category has multiple building types..
        $buildingTypes = BuildingType::where('building_type_category_id', $answer)->get();

        // when there is only one building type, we have to save that for the building
        if ($buildingTypes->count() <= 1) {
            $buildingTypeCategory = \App\Models\BuildingTypeCategory::find($answer);

            $buildingType = $buildingTypes->first();
            Log::debug("Only 1 building type found for category {$buildingTypeCategory->name}, lets set the $buildingType->name as building type.");
            return ['building_type_id' => $buildingType->id];
        }

        return [];
    }
}