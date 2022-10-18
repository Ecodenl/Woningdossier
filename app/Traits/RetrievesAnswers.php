<?php

namespace App\Traits;

use App\Helpers\Conditions\ConditionEvaluator;
use App\Models\Building;
use App\Models\InputSource;
use App\Models\ToolQuestion;

trait RetrievesAnswers
{
    public Building $building;

    public InputSource $inputSource;

    /**
     * Get the answer from the given building (if allowed).
     *
     * @param  string  $toolQuestionShort
     *
     * @return array|mixed
     */
    protected function getAnswer(string $toolQuestionShort)
    {
        $toolQuestion = ToolQuestion::findByShort($toolQuestionShort);
        $conditions = $toolQuestion->subSteps()->first()->pivot->conditions;

        $evaluation = ConditionEvaluator::init()->building($this->building)->inputSource($this->inputSource)
            ->evaluate($conditions);

        return $evaluation ? $this->building->getAnswer(
            $this->inputSource,
            $toolQuestion
        ) : null;
    }

    /**
     * Static wrapper for getAnswer. NOTE: This ONLY works for empty constructor classes!
     *
     * @return array|mixed
     */
    protected static function getQuickAnswer(string $toolQuestionShort, Building $building, InputSource $inputSource)
    {
        $instance = new static;
        $instance->building = $building;
        $instance->inputSource = $inputSource;

        return $instance->getAnswer($toolQuestionShort);
    }
}