<?php

namespace App\Observers\ToolQuestionAnswer;

use App\Models\BuildingType;
use App\Models\ToolQuestionAnswer;

class BuildingTypeCategory implements ShouldApply
{
    public static function apply(ToolQuestionAnswer $toolQuestionAnswer)
    {
        // check if the building type category has multiple building types..
        $buildingTypes = BuildingType::where('building_type_category_id', $toolQuestionAnswer->answer)->get();
        // when there is only one building type, we have to save that for the building
        if ($buildingTypes->count() <= 1) {
            $toolQuestionAnswer
                ->building
                ->buildingFeatures()
                ->forInputSource($toolQuestionAnswer->inputSource)
                ->update(['building_type_id' => $buildingTypes->first()->id]);
        }
    }
}