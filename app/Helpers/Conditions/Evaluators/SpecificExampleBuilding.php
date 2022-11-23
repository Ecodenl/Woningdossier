<?php

namespace App\Helpers\Conditions\Evaluators;

use App\Models\Building;
use App\Models\ExampleBuilding;
use App\Models\InputSource;
use App\Models\ToolQuestion;
use Illuminate\Support\Collection;

class SpecificExampleBuilding implements ShouldEvaluate
{
    public static function evaluate(Building $building, InputSource $inputSource, $value = null, ?Collection $answers = null): bool
    {
        $buildingTypeId = $building->getAnswer(
            $inputSource,
            ToolQuestion::findByShort('building-type')
        );

        $specificExampleBuildingExists = ExampleBuilding::where('cooperation_id', $building->user->cooperation_id)
            ->where('building_type_id', '=', $buildingTypeId)
            ->exists();

        $genericExampleBuildingCountForBuildingType = ExampleBuilding::where('building_type_id', '=', $buildingTypeId)
            ->whereNull('cooperation_id')
            ->count();

        // when there is only 1 generic example building and no specific example buildings we want to skip the step
        // because there are just no choices to be made here.
        return $specificExampleBuildingExists || $genericExampleBuildingCountForBuildingType > 1;
    }
}