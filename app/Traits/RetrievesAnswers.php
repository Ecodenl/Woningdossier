<?php

namespace App\Traits;

use App\Models\Building;
use App\Models\InputSource;
use App\Models\ToolQuestion;

trait RetrievesAnswers
{
    public Building $building;

    public InputSource $inputSource;

    /**
     * Get the answer from the given building.
     *
     * @param  string  $toolQuestion
     *
     * @return array|mixed
     */
    protected function getAnswer(string $toolQuestion)
    {
        return $this->building->getAnswer(
            $this->inputSource,
            ToolQuestion::findByShort($toolQuestion)
        );
    }

    /**
     * Static wrapper for getAnswer. NOTE: This ONLY works for empty constructor classes!
     *
     * @return array|mixed
     */
    protected static function getQuickAnswer(string $toolQuestion, Building $building, InputSource $inputSource)
    {
        $instance = new static;
        $instance->building = $building;
        $instance->inputSource = $inputSource;

        return $instance->getAnswer($toolQuestion);
    }
}