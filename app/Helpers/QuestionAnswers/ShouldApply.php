<?php

namespace App\Helpers\QuestionAnswers;

use App\Models\ToolQuestion;

interface ShouldApply
{
    public static function apply(ToolQuestion $toolQuestion, $answer): array;
}