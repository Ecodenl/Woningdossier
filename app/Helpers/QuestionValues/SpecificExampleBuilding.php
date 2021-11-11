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
        // get the current example building for the user
        $buildingFeature = $building->buildingFeatures()->forInputSource($inputSource)->first();

        $conditionalQuestion = ToolQuestion::findByShort('building-type');
        $cooperationId = $building->user->cooperation_id;

        $buildingTypeId = $building->getAnswer($inputSource, $conditionalQuestion);


        $specificExampleBuildings = $questionValues->where('building_type_id', $buildingTypeId)->where('cooperation_id', $cooperationId);
        $genericExampleBuildings = $questionValues
            ->where('building_type_id', $buildingTypeId)
            ->whereNull('cooperation_id')
            ->where('value', '!=', $buildingFeature->example_building_id);

        // now we will change the current selected GENERIC example building name to "Geen van deze"
        $currentExampleBuilding = $questionValues
            ->where('building_type_id', $buildingTypeId)
            ->whereNull('cooperation_id')
            ->where('value', $buildingFeature->example_building_id)
            ->first();

        // its an array when its found.
        if (is_array($currentExampleBuilding)) {
            $currentExampleBuilding['name'] = "Geen van de overige opties.";
            $genericExampleBuildings->push($currentExampleBuilding);
        }

        return $genericExampleBuildings->merge($specificExampleBuildings);
    }
}