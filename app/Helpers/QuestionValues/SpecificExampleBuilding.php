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


        // Get all available example buildings
        $exampleBuildings = ExampleBuilding::where('building_type_id', $buildingTypeId)
            ->where(function ($query) use ($cooperationId) {
                $query->whereNull('cooperation_id')
                    ->orWhere('cooperation_id', $cooperationId);
            })
            ->get();

        // Map it to question values
        return $exampleBuildings->map(function ($exampleBuilding) {
            return [
                'building_type_id' => $exampleBuilding->building_type_id,
                'cooperation_id' => $exampleBuilding->cooperation_id,
                'extra' => [
                    'icon' => 'icon-not-relevant',
                ],
                'name' => $exampleBuilding->name,
                'value' => $exampleBuilding->id,
            ];
        });
    }
}