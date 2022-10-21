<?php

namespace App\Traits;

use App\Helpers\DataTypes\Caster;
use App\Models\Building;
use App\Models\InputSource;
use App\Models\ToolQuestion;
use App\Services\ConditionService;
use Illuminate\Support\Collection;

trait HasDynamicAnswers
{
    public ?Collection $answers = null;

    /**
     * Get the answer, either dynamic if set, otherwise from the given building.
     *
     * @param string $toolQuestionShort
     *
     * @return array|mixed
     */
    protected function getAnswer(string $toolQuestionShort)
    {
        $answers = is_null($this->answers) ? collect() : $this->answers;
        $toolQuestion = ToolQuestion::findByShort($toolQuestionShort);

        // If the answer exists, we want to ensure we format it correctly for backend use
        if ($answers->has($toolQuestionShort)) {
            $answer = $answers->get($toolQuestionShort);

            if (in_array($toolQuestion->data_type, [Caster::INT, Caster::FLOAT])) {
                $answer = Caster::init($toolQuestion->data_type, $answer)->reverseFormatted();
            }

            return $answer;
        } else {
            $evaluation = ConditionService::init()
                ->building($this->building)->inputSource($this->inputSource)
                ->forModel($toolQuestion)->isViewable($answers);

            return $evaluation ? $this->building->getAnswer(
                $this->inputSource,
                $toolQuestion
            ) : null;
        }
    }

    /**
     * Static wrapper for getAnswer. NOTE: This ONLY works for empty constructor classes!
     *
     * @return array|mixed
     */
    protected static function getQuickAnswer(string $toolQuestionShort, Building $building, InputSource $inputSource, ?Collection $answers = null)
    {
        $instance = new static;
        $instance->building = $building;
        $instance->inputSource = $inputSource;
        $instance->answers = $answers;

        return $instance->getAnswer($toolQuestionShort);
    }
}