<?php

namespace App\Helpers\QuestionValues;

use App\Models\Building;
use App\Models\InputSource;
use App\Models\ToolQuestion;
use Illuminate\Support\Collection;

class SpecificExampleBuilding implements ShouldReturnQuestionValues
{
    public static function getQuestionValues(Collection $questionValues, Building $building, InputSource $inputSource): Collection
    {
        $conditionalQuestion = ToolQuestion::findByShort('building-type');
        $cooperationId = $building->user->cooperation_id;

        $buildingTypeId = $building->getAnswer($inputSource, $conditionalQuestion);

        $specificExampleBuildings = $questionValues->where('building_type_id', $buildingTypeId)->where('cooperation_id', $cooperationId);
        $genericExampleBuildings = $questionValues->where('building_type_id', $buildingTypeId)->whereNull('cooperation_id');

        return $genericExampleBuildings->merge($specificExampleBuildings);
    }
}