<?php

namespace App\Helpers\QuestionValues;

use App\Models\Cooperation;
use App\Traits\FluentCaller;
use App\Traits\HasDynamicAnswers;
use App\Traits\Services\HasBuilding;
use App\Traits\Services\HasInputSources;
use Illuminate\Support\Collection;

abstract class QuestionValuable implements ShouldReturnQuestionValues
{
    use FluentCaller,
        HasBuilding,
        HasInputSources,
        HasDynamicAnswers;

    public Cooperation $cooperation;
    public Collection $questionValues;

    public function __construct(Cooperation $cooperation, Collection $questionValues, Collection $answers = null)
    {
        $this->cooperation = $cooperation;
        $this->questionValues = $questionValues;
        $this->answers = $answers;
    }
}