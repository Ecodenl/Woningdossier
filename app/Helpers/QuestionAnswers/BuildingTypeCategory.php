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

            /** @var BuildingType $buildingType */
            $buildingType = $buildingTypes->first();
            Log::debug("Only 1 building type found for category {$buildingTypeCategory->getTranslation('name', 'nl')}, lets set the {$buildingType->getTranslation('name', 'nl')} as building type.");
            return ['building_type_id' => $buildingType->id];
        }

        return [];
    }
}
