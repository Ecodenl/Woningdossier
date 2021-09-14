<?php

namespace App\Observers;

use App\Models\ToolQuestionAnswer;
use Illuminate\Support\Str;

class ToolQuestionAnswerObserver
{
    /**
     * Handle the tool question answer "created" event.
     *
     * @param \App\ToolQuestionAnswer $toolQuestionAnswer
     * @return void
     */
    public function created(ToolQuestionAnswer $toolQuestionAnswer)
    {

    }

    /**
     * Handle the tool question answer "updated" event.
     *
     * @param \App\ToolQuestionAnswer $toolQuestionAnswer
     * @return void
     */
    public function updated(ToolQuestionAnswer $toolQuestionAnswer)
    {
        $toolQuestion = $toolQuestionAnswer->toolQuestion;
        if ($toolQuestionAnswer->inputSource->short != 'master') {

            $className = Str::studly($toolQuestion->short);
            $questionValuesClass = "App\\Observers\\ToolQuestionAnswer\\{$className}";

            if (class_exists($questionValuesClass)) {
                $questionValuesClass::apply($toolQuestionAnswer);
        }
        }
    }

}
