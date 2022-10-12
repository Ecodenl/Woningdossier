<?php

namespace App\Helpers\QuestionValues;

use App\Helpers\Cooperation\Tool\VentilationHelper;
use App\Helpers\Cooperation\Tool\WallInsulationHelper;
use App\Models\Building;
use App\Models\BuildingType as BuildingTypeModel;
use App\Models\InputSource;
use App\Models\ToolQuestion;
use Illuminate\Support\Collection;

class WallFacadePlasteredPainted extends QuestionValuable
{
    public function getQuestionValues(): Collection
    {
        $values = collect();

        foreach(WallInsulationHelper::getFacadePlasteredPaintedValues() as $value => $name) {
            $values->push(compact('value', 'name'));
        }

        return $values;
    }
}