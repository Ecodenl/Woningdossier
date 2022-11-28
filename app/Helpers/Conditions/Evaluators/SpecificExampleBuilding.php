<?php

namespace App\Helpers\Conditions\Evaluators;

use App\Models\ExampleBuilding;
use App\Models\ToolQuestion;
use Illuminate\Support\Collection;

class SpecificExampleBuilding extends ShouldEvaluate
{
    public function evaluate($value = null, ?Collection $answers = null): array
    {
        $building = $this->building;
        $inputSource = $this->inputSource;

        $key = md5(json_encode([null]));

        if (array_key_exists($key, $this->override)) {
            $results = $this->override[$key];
            return [
                'results' => $results,
                'bool' => $results['specific_exists'] || $results['generic_total'] > 1,
                'key' => $key,
            ];
        }

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

        $results = [
            'specific_exists' => $specificExampleBuildingExists,
            'generic_total' => $genericExampleBuildingCountForBuildingType,
        ];

        // when there is only 1 generic example building and no specific example buildings we want to skip the step
        // because there are just no choices to be made here.
        return [
            'results' => $results,
            'bool' => $results['specific_exists'] || $results['generic_total'] > 1,
            'key' => $key,
        ];
    }
}