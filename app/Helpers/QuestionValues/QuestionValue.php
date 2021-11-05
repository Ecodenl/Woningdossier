<?php

namespace App\Helpers\QuestionValues;

use App\Models\Building;
use App\Models\InputSource;
use App\Models\ToolQuestion;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class QuestionValue {

    public static function getQuestionValues(ToolQuestion $toolQuestion, Building $building, InputSource $inputSource, Model $limitedTo): Collection
    {
        $questionValues = $toolQuestion->getQuestionValues($limitedTo);

        $className = Str::studly($toolQuestion->short);
        $questionValuesClass = "App\\Helpers\\QuestionValues\\{$className}";

        if (class_exists($questionValuesClass)) {
            $questionValues = $questionValuesClass::getQuestionValues(
                $questionValues,
                $building,
                $inputSource
            );
        }

        return $questionValues;
    }
}