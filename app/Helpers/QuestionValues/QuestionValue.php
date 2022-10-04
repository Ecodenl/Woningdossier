<?php

namespace App\Helpers\QuestionValues;

use App\Helpers\Conditions\ConditionEvaluator;
use App\Models\Building;
use App\Models\InputSource;
use App\Models\ToolQuestion;
use App\Traits\FluentCaller;
use App\Traits\HasDynamicAnswers;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class QuestionValue extends QuestionValuable
{
    public function getQuestionValues(): Collection
    {
        $toolQuestion = $this->toolQuestion;
        $questionValues = $toolQuestion->getQuestionValues();

        $className = Str::studly($toolQuestion->short);
        $questionValuesClass = "App\\Helpers\\QuestionValues\\{$className}";

        if (class_exists($questionValuesClass)) {
            $questionValues = $questionValuesClass::init($questionValues, $this->cooperation, $this->evaluatableAnswers)
                ->getQuestionValues();
        }

        if ($this->withCustomEvaluation()) {

            $evaluator = ConditionEvaluator::init()
                ->inputSource($this->inputSource)
                ->building($this->building);

            foreach ($questionValues as $index => $questionValue) {
                if (!empty($questionValue['conditions'])) {
                    $passed = $evaluator->evaluateCollection(
                        $questionValue['conditions'],
                        $evaluator->getToolAnswersForConditions($questionValue['conditions'])->merge($this->evaluatableAnswers)
                    );

                    if (!$passed) {
                        $questionValues->forget($index);
                    }
                }
            }
        }

        return $questionValues;
    }
}