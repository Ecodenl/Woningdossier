<?php

namespace App\Helpers\QuestionValues;

use App\Models\ExampleBuilding;
use Illuminate\Support\Collection;

class SpecificExampleBuilding extends QuestionValuable
{
    public function getQuestionValues(): Collection
    {
        $buildingTypeId = $this->getAnswer('building-type', false);
        $cooperationId = $this->cooperation->id;

        // Building type ID can be null, for example if we use $building->getAnswerForAllInputSources, it can
        // end up with an input source that might not have answered this question yet. Since we only want
        // example buildings for the given type anyway, we don't have to do anything if the ID is null
        if (! is_null($buildingTypeId)) {
            // There should only ever be one building. If there's more, well, then they fucked it up themselves
            $genericBuilding = ExampleBuilding::where('building_type_id', $buildingTypeId)
                ->whereNull('cooperation_id')
                ->first();

            // TODO: use generic() scope
            // TODO: use select(*)->addSelect(DB::RAW(trans as name)), this allows us to skip the boolean check on line 54

            // Get all available example buildings
            $exampleBuildings = ExampleBuilding::where('building_type_id', $buildingTypeId)
                ->where('cooperation_id', $cooperationId)
                ->get();

            // If it's not an example building, it will add null to the collection which isn't
            // great when trying to use it as object. Even though this shouldn't happen, we do this
            // as a precaution.
            if ($genericBuilding instanceof ExampleBuilding) {
                // Always add generic building last
                $exampleBuildings->add($genericBuilding);
            }

            // Map it to question values
            return $exampleBuildings->map(function ($exampleBuilding) use ($genericBuilding) {
                return [
                    'building_type_id' => $exampleBuilding->building_type_id,
                    'cooperation_id' => $exampleBuilding->cooperation_id,
                    'extra' => [
                        'icon' => 'icon-not-relevant',
                    ],
                    'name' => $genericBuilding?->id === $exampleBuilding->id ? __('cooperation/frontend/tool/simple-scan/question-values.specific-example-building.no-option') : $exampleBuilding->name,
                    'value' => $exampleBuilding->id,
                ];
            });
        }

        return collect([]);
    }
}