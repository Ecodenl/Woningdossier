<?php

namespace App\Helpers\QuestionValues;

use App\Models\Building;
use App\Models\ExampleBuilding;
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

        // There should only ever be one building. If there's more, well, then they fucked it up themselves
        $genericBuilding = ExampleBuilding::where('building_type_id', $buildingTypeId)
            ->whereNull('cooperation_id')
            ->first();

        // Get all available example buildings
        $exampleBuildings = ExampleBuilding::where('building_type_id', $buildingTypeId)
            ->where('cooperation_id', $cooperationId)
            ->get();

        // Always add generic building last
        $exampleBuildings->add($genericBuilding);

        // Map it to question values
        return $exampleBuildings->map(function ($exampleBuilding) use ($genericBuilding) {
            return [
                'building_type_id' => $exampleBuilding->building_type_id,
                'cooperation_id' => $exampleBuilding->cooperation_id,
                'extra' => [
                    'icon' => 'icon-not-relevant',
                ],
                'name' => $genericBuilding->id === $exampleBuilding->id ? __('cooperation/frontend/tool/quick-scan/question-values.specific-example-building.no-option') : $exampleBuilding->name,
                'value' => $exampleBuilding->id,
            ];
        });
    }
}