<?php

namespace App\Calculations;

use App\Models\Building;
use App\Models\InputSource;
use App\Models\ToolQuestion;
use App\Models\UserEnergyHabit;
use Illuminate\Support\Collection;

abstract class Calculator
{
    public Building $building;

    public InputSource $inputSource;

    // TODO: Make uniform
    //public UserEnergyHabit $energyHabit;

    public ?Collection $answers = null;

    public array $calculateData = [];

    // TODO: Uniform constructor

    // TODO: For now, no uniform amount of parameters is used, so we can't make it abstract
    // abstract public static function calculate();

    /**
     * Get the answer, either dynamic if set, otherwise from the given building.
     *
     * @param  string  $toolQuestion
     *
     * @return array|mixed
     */
    protected function getAnswer(string $toolQuestion)
    {
        $answers = is_null($this->answers) ? collect() : $this->answers;

        return $answers->has($toolQuestion) ? $answers->get($toolQuestion) :
            $this->building->getAnswer(
                $this->inputSource,
                ToolQuestion::findByShort($toolQuestion)
            );
    }
}