<?php

namespace App\Traits;

use App\Models\Building;
use App\Models\InputSource;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

trait HasDynamicAnswers
{
    use RetrievesAnswers {
        getAnswer as getBuildingAnswer;
    }

    public ?Collection $answers = null;

    /**
     * Get the answer, either dynamic if set, otherwise from the given building.
     *
     * @param string $toolQuestion
     *
     * @return array|mixed
     */
    protected function getAnswer(string $toolQuestion)
    {
        $answers = is_null($this->answers) ? collect() : $this->answers;

        return $answers->has($toolQuestion) ? $answers->get($toolQuestion) :
            $this->getBuildingAnswer($toolQuestion);
    }

    /**
     * Static wrapper for getAnswer. NOTE: This ONLY works for empty constructor classes!
     *
     * @return array|mixed
     */
    protected static function getQuickAnswer(string $toolQuestion, Building $building, InputSource $inputSource, ?Collection $answers = null)
    {
        $instance = new static;
        $instance->building = $building;
        $instance->inputSource = $inputSource;
        $instance->answers = $answers;

        return $instance->getAnswer($toolQuestion);
    }
}