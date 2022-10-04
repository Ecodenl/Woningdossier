<?php

namespace App\Helpers\QuestionValues;

use App\Models\Building;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\ToolQuestion;
use App\Traits\FluentCaller;

class QuestionValuable
{
    use FluentCaller;

    public ToolQuestion $toolQuestion;
    public Cooperation $cooperation;
    public array $evaluatableAnswers;

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

    public function evaluateableAnswers(array $answers)
    {
        $this->evaluatableAnswers = $answers;
    }

    public function withCustomEvaluation(): self
    {
        $this->customEvaluation = true;
        return $this;
    }

}