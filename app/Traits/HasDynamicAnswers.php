<?php

namespace App\Traits;

use App\Helpers\DataTypes\Caster;
use App\Models\Building;
use App\Models\InputSource;
use App\Models\ToolQuestion;
use Illuminate\Support\Collection;

trait HasDynamicAnswers
{
    use RetrievesAnswers {
        getAnswer as getBuildingAnswer;
        getManyAnswers as getManyBuildingAnswers;
    }

    public ?Collection $answers = null;

    /**
     * Get the answer, either dynamic if set, otherwise from the given building.
     *
     * @param string $toolQuestionShort
     * @param bool $withEvaluation Because sometimes, but ONLY sometimes we don't want validation
     *
     * @return array|mixed
     */
    protected function getAnswer(string $toolQuestionShort, bool $withEvaluation = true)
    {
        $answers = is_null($this->answers) ? collect() : $this->answers;
        $toolQuestion = ToolQuestion::findByShort($toolQuestionShort);

        // If the answer exists, we want to ensure we format it correctly for backend use
        if ($answers->has($toolQuestionShort)) {
            // TODO: Answer might be set but perhaps not answerable? Should we evaluate here too?
            $answer = $answers->get($toolQuestionShort);

            if (in_array($toolQuestion->data_type, [Caster::INT, Caster::FLOAT])) {
                $answer = Caster::init($toolQuestion->data_type, $answer)->reverseFormatted();
            }
        } else {
            $answer = $this->getBuildingAnswer($toolQuestionShort, $withEvaluation);
        }

        // Even if we can't answer the question, we want this cast
        if (in_array($toolQuestion->data_type, [Caster::INT, Caster::FLOAT])) {
            $answer = Caster::init($toolQuestion->data_type, $answer)->force()->getCast();
        }

        return $answer;
    }

    protected function getManyAnswers(array $toolQuestionShorts, bool $withEvaluation = true): array
    {
        $answers = is_null($this->answers) ? collect() : $this->answers;

        $toolQuestionAnswers = [];

        foreach ($toolQuestionShorts as $index => $toolQuestionShort) {
            $toolQuestion = ToolQuestion::findByShort($toolQuestionShort);

            if ($answers->has($toolQuestionShort)) {
                // TODO: Answer might be set but perhaps not answerable? Should we evaluate here too?
                $answer = $answers->get($toolQuestionShort);

                if (in_array($toolQuestion->data_type, [Caster::INT, Caster::FLOAT])) {
                    $answer = Caster::init($toolQuestion->data_type, $answer)->reverseFormatted();
                }
                $toolQuestionAnswers[$toolQuestionShort] = $answer;
                unset($toolQuestionShorts[$index]);
            }
        }

        // Still some answers left unanswered
        if (! empty($toolQuestionShorts)) {
            $toolQuestionAnswers = array_merge($toolQuestionAnswers,
                $this->getManyBuildingAnswers($toolQuestionShorts, $withEvaluation));
        }

        // Cast data
        foreach ($toolQuestionAnswers as $short => $answer) {
            $toolQuestion = ToolQuestion::findByShort($short);
            if (in_array($toolQuestion->data_type, [Caster::INT, Caster::FLOAT])) {
                $toolQuestionAnswers[$short] = Caster::init($toolQuestion->data_type, $answer)->force()->getCast();
            }
        }

        return $toolQuestionAnswers;
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