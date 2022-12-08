<?php

namespace App\Helpers\Cooperation\Tool;

use App\Models\BuildingHeater;
use App\Models\Step;
use App\Services\ConditionService;
use App\Services\UserActionPlanAdviceService;

class SmallMeasureHelper extends ToolHelper
{
    public function saveValues(): ToolHelper
    {
        // Format isn't applicable for this helper, but it is required due to abstraction
        return $this;
    }

    public function createValues(): ToolHelper
    {
        $this->setValues([

            'updated_measure_ids' => [],
        ]);

        return $this;
    }

    public function createAdvices(): ToolHelper
    {
        $updatedMeasureIds = $this->getValues('updated_measure_ids');

        // NOTE: This will ALWAYS return the quick scan step as that's the first step available in the database.
        // This is also the step the measures are saved on.
        $step = Step::findByShort('small-measures');
        $oldAdvices = UserActionPlanAdviceService::clearForStep($this->user, $this->inputSource, $step);

        // How does one consider a small measure measure? Simple, by answering a related answer as "already-do" or
        // "want-to". Because consistency!

        //TODO: Consider by answer
        // Create measure
        // Find all tool questions in one go, findManyAnswers in trait perhaps


        return $this;
    }
}
