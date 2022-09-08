<?php

namespace App\Calculations;

use App\Traits\HasDynamicAnswers;

abstract class Calculator
{
    use HasDynamicAnswers;

    // TODO: Make uniform
    //public UserEnergyHabit $energyHabit;

    public array $calculateData = [];

    // TODO: Uniform constructor

    // TODO: For now, no uniform amount of parameters is used, so we can't make it abstract
    // abstract public static function calculate();
}