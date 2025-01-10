<?php

namespace App\Observers;

use App\Models\InputSource;
use App\Models\ToolQuestionAnswer;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ToolQuestionAnswerObserver
{
    public function saved(ToolQuestionAnswer $toolQuestionAnswer)
    {
        $this->checkForCustomLogic($toolQuestionAnswer);
    }

    private function checkForCustomLogic(ToolQuestionAnswer $toolQuestionAnswer)
    {
        $toolQuestion = $toolQuestionAnswer->toolQuestion;
        if ($toolQuestionAnswer->inputSource->short != InputSource::MASTER_SHORT) {
            $className = Str::studly($toolQuestion->short);
            $questionValuesClass = "App\\Observers\\ToolQuestionAnswer\\{$className}";

            if (class_exists($questionValuesClass)) {
                Log::debug("Custom observer triggered for {$toolQuestion->short} tool_question_answer data:" . json_encode($toolQuestionAnswer->getAttributes()));
                $questionValuesClass::apply($toolQuestionAnswer);
            }
        }
    }
}
