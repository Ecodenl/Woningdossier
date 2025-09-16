<?php

namespace App\Observers\ToolQuestionAnswer;

use App\Models\ToolQuestionAnswer;
use App\Services\UserActionPlanAdviceService;

class BuildingContractType implements ShouldApply
{
    public static function apply(ToolQuestionAnswer $toolQuestionAnswer)
    {
        $user = $toolQuestionAnswer->building->user;

        // We don't need to do this for master, else it triggers twice. The service already handles ALL advices
        UserActionPlanAdviceService::init()
            ->forUser($user)
            ->refreshUserRegulations();
    }
}
