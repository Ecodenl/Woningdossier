<?php

namespace App\Helpers\QuestionValues;

use App\Helpers\ConsiderableHelper;
use Illuminate\Support\Collection;

class IsConsidering extends QuestionValuable
{
    public function getQuestionValues(): Collection
    {
        $values = collect();

        foreach(ConsiderableHelper::getConsiderableValues() as $value => $name) {
            $values->push(compact('value', 'name'));
        }

        return $values;
    }
}