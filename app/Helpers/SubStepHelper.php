<?php

namespace App\Helpers;

use App\Helpers\Conditions\ConditionEvaluator;
use App\Models\Building;
use App\Models\CompletedSubStep;
use App\Models\InputSource;
use App\Models\Step;
use App\Models\SubStep;
use App\Models\SubSteppable;
use App\Models\ToolQuestion;

class SubStepHelper
{
    /**
     * Complete a sub step for a building.
     *
     * @param \App\Models\SubStep $subStep
     * @param \App\Models\Building $building
     * @param \App\Models\InputSource $inputSource
     *
     * @return void
     */
    public static function complete(SubStep $subStep, Building $building, InputSource $inputSource)
    {
        CompletedSubStep::allInputSources()->firstOrCreate([
            'sub_step_id' => $subStep->id,
            'input_source_id' => $inputSource->id,
            'building_id' => $building->id,
        ]);
    }

    /**
     * Incomplete a step for a building.
     *
     * @param \App\Models\SubStep $subStep
     * @param \App\Models\Building $building
     * @param \App\Models\InputSource $inputSource
     *
     * @return void
     * @throws \Exception
     */
    public static function incomplete(SubStep $subStep, Building $building, InputSource $inputSource)
    {
        CompletedSubStep::allInputSources()->where([
            'sub_step_id' => $subStep->id,
            'input_source_id' => $inputSource->id,
            'building_id' => $building->id,
        ])->delete();
    }
}
