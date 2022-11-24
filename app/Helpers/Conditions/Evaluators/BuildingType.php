<?php

namespace App\Helpers\Conditions\Evaluators;

use App\Models\Building;
use App\Models\InputSource;
use App\Models\ToolQuestion;
use App\Models\BuildingType as BuildingTypeModel;
use Illuminate\Support\Collection;

class BuildingType implements ShouldEvaluate
{
    public static function evaluate(Building $building, InputSource $inputSource, $value = null, ?Collection $answers = null): bool
    {
        // check what kind of category the user has selected, it will determine whether we have to show the building type or not.
        $buildingTypeCategoryId = $building->getAnswer(
            $inputSource,
            ToolQuestion::findByShort('building-type-category')
        );

        // only one option would mean that the building type category = building type
        // if there are multiple building types the user has to select a specific one
        return BuildingTypeModel::where('building_type_category_id', $buildingTypeCategoryId)->count() > 1;
    }
}