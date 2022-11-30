<?php

namespace App\Helpers\Conditions\Evaluators;

use App\Models\ToolQuestion;
use App\Models\BuildingType as BuildingTypeModel;
use Illuminate\Support\Collection;

class BuildingType extends ShouldEvaluate
{
    public function evaluate($value = null): array
    {
        $building = $this->building;
        $inputSource = $this->inputSource;
        $answers = $this->answers;

        $key = md5(json_encode([null]));

        if (array_key_exists($key, $this->override)) {
            $totalCategories = $this->override[$key];
            return [
                'results' => $totalCategories,
                'bool' => $totalCategories > 1,
                'key' => $key,
            ];
        }

        // check what kind of category the user has selected, it will determine whether we have to show the building type or not.
        $buildingTypeCategoryId = $building->getAnswer(
            $inputSource,
            ToolQuestion::findByShort('building-type-category')
        );

        $totalCategories = BuildingTypeModel::where('building_type_category_id', $buildingTypeCategoryId)->count();

        // only one option would mean that the building type category = building type
        // if there are multiple building types the user has to select a specific one
        return [
            'results' => $totalCategories,
            'bool' => $totalCategories > 1,
            'key' => $key,
        ];
    }
}