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

        return $questionValues
            ->filter(function (array $questionValue) use ($buildingTypeId, $cooperationId) {

                $matchesBuildingTypeAndIsForUserItsCooperation = ($questionValue['building_type_id'] == $buildingTypeId && $questionValue['cooperation_id'] == $cooperationId);
                $matchedBuildingTypeButIsGeneric = ($questionValue['building_type_id'] == $buildingTypeId && is_null($questionValue['cooperation_id']));


                return $matchesBuildingTypeAndIsForUserItsCooperation || $matchedBuildingTypeButIsGeneric;
            });

    }
}