<?php

namespace App\Helpers\QuestionValues;

use App\Models\Building;
use App\Models\BuildingType as BuildingTypeModel;
use App\Models\InputSource;
use App\Models\ToolQuestion;
use Illuminate\Support\Collection;

class BuildingType implements ShouldReturnQuestionValues
{
    public static function getQuestionValues(Collection $questionValues, Building $building): Collection
    {
        $conditionalQuestion = ToolQuestion::findByShort('building-type-category');

        $buildingTypeCategoryId = $building->getAnswer($inputSource, $conditionalQuestion);

        // only one option would mean there are no multiple building types for the category, thus the page is redundant.
        // so multiple building types = next step.
        $matchedBuildingType = BuildingTypeModel::where('building_type_category_id', $buildingTypeCategoryId)->get();
        return $questionValues->whereIn('value', $matchedBuildingType->pluck('id')->toArray());
    }
}