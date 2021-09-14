<?php

namespace App\Observers\ToolQuestionAnswer;

use App\Models\ToolQuestionAnswer;

interface ShouldApply
{
    public static function apply(ToolQuestionAnswer $toolQuestionAnswer);
}