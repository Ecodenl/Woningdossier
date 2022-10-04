<?php

namespace App\Helpers\QuestionValues;

use App\Helpers\Conditions\ConditionEvaluator;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\ToolQuestion;
use App\Traits\FluentCaller;
use App\Traits\HasDynamicAnswers;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class QuestionValue
{
    use FluentCaller;

    public ToolQuestion $toolQuestion;
    public Cooperation $cooperation;
    public ?Collection $answers = null;

    // this bool will determine if we will "dumbly" return all the available options
    // or check if we should only return specific options.
    public bool $customEvaluation = false;
    public Building $building;
    public InputSource $inputSource;

    public function __construct(Cooperation $cooperation, ToolQuestion $toolQuestion)
    {
        $this->cooperation = $cooperation;
        $this->toolQuestion = $toolQuestion;
    }

    public function answers(Collection $answers): self
    {
        $this->answers = $answers;
        return $this;
    }

    public function withCustomEvaluation(): self
    {
        $this->customEvaluation = true;
        return $this;
    }

    public function getQuestionValues(): Collection
    {
        $toolQuestion = $this->toolQuestion;
        $questionValues = $toolQuestion->getQuestionValues();

        $className = Str::studly($toolQuestion->short);
        $questionValuesClass = "App\\Helpers\\QuestionValues\\{$className}";

        if (class_exists($questionValuesClass)) {
            $questionValues = $questionValuesClass::init($questionValues, $this->cooperation, $this->answers)
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