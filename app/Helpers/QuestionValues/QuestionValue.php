<?php

namespace App\Helpers\QuestionValues;

use App\Helpers\Conditions\ConditionEvaluator;
use App\Models\Building;
use App\Models\InputSource;
use App\Models\ToolQuestion;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class QuestionValue
{
    public static function getQuestionValues(ToolQuestion $toolQuestion, Building $building, InputSource $inputSource, ?Collection $answers = null): Collection
    {
        $questionValues = $toolQuestion->getQuestionValues();

        $className = Str::studly($toolQuestion->short);
        $questionValuesClass = "App\\Helpers\\QuestionValues\\{$className}";

        if (class_exists($questionValuesClass)) {
            $questionValues = $questionValuesClass::getQuestionValues(
                $questionValues,
                $building,
                $inputSource,
                $answers
            );
        }

        $evaluator = ConditionEvaluator::init()
            ->inputSource($inputSource)
            ->building($building);

        foreach ($questionValues as $index => $questionValue) {
            if (! empty($questionValue['conditions'])) {
                $passed = $evaluator->evaluateCollection(
                    $questionValue['conditions'],
                    $evaluator->getToolAnswersForConditions($questionValue['conditions'])->merge($answers)
                );

                if (! $passed) {
                    $questionValues->forget($index);
                }
            }
        }

        return $questionValues;
    }
}