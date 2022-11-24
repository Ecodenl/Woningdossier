<?php

namespace App\Traits;

use App\Models\Building;
use App\Models\InputSource;
use App\Models\ToolQuestion;
use App\Services\ConditionService;

trait RetrievesAnswers
{
    public Building $building;

    public InputSource $inputSource;

    /**
     * Get the answer from the given building (if allowed).
     *
     * @param  string  $toolQuestionShort
     * @param bool $withEvaluation Because sometimes, but ONLY sometimes we don't want validation
     *
     * @return array|mixed
     */
    protected function getAnswer(string $toolQuestionShort, bool $withEvaluation = true)
    {
        $toolQuestion = ToolQuestion::findByShort($toolQuestionShort);

        $evaluation = true;
        if ($withEvaluation) {
            $evaluation = ConditionService::init()
                ->building($this->building)->inputSource($this->inputSource)
                ->forModel($toolQuestion)->isViewable();
        }

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