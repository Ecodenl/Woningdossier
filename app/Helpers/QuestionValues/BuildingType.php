<?php

namespace App\Helpers\QuestionValues;

use App\Models\Building;
use App\Models\BuildingType as BuildingTypeModel;
use App\Models\InputSource;
use App\Models\ToolQuestion;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class BuildingType extends QuestionValuable
{
    public function getQuestionValues(): Collection
    {
        $buildingTypeCategoryId = $this->getAnswer('building-type-category');
        Log::debug("Found a building type category with ID: {$buildingTypeCategoryId}");

        // only one option would mean there are no multiple building types for the category, thus the page is redundant.
        // so multiple building types = next step.
        $matchedBuildingType = BuildingTypeModel::where('building_type_category_id', $buildingTypeCategoryId)->get();
        return $this->questionValues->whereIn('value', $matchedBuildingType->pluck('id')->toArray());
    }
}