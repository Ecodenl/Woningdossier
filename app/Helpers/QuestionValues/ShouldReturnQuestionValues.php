<?php

namespace App\Helpers\QuestionValues;

use App\Models\Building;
use App\Models\InputSource;
use Illuminate\Support\Collection;

interface ShouldReturnQuestionValues
{
    /*
     * @param Collection $questionValues (base question values from toolQuestion->getQuestionValues())
     * @return Collection
     */
    public function getQuestionValues(): Collection;
}