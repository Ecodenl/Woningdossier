<?php

namespace App\Helpers\QuestionValues;

use App\Models\Building;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Traits\FluentCaller;
use App\Traits\HasDynamicAnswers;
use Illuminate\Support\Collection;

abstract class QuestionValuable implements ShouldReturnQuestionValues
{
    use FluentCaller, HasDynamicAnswers;

    public Cooperation $cooperation;
    public Collection $questionValues;

    public function __construct(Cooperation $cooperation, Collection $questionValues, Collection $answers = null)
    {
        $this->cooperation = $cooperation;
        $this->questionValues = $questionValues;
        $this->answers = $answers;
    }

    public function forInputSource(InputSource $inputSource): self
    {
        $this->inputSource = $inputSource;
        return $this;
    }

    public function forBuilding(Building $building): self
    {
        $this->building = $building;
        return $this;
    }

}