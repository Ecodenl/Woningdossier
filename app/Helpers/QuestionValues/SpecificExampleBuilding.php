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

        $buildingTypeId = $building->getAnswer(
            $inputSource,
            $conditionalQuestion
        );

        return $questionValues->where('building_type_id', $buildingTypeId)->where('cooperation_id', $building->user->cooperation_id);
    }
}