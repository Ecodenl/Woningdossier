<?php

namespace App\Helpers\QuestionValues;

use App\Models\Cooperation;
use App\Traits\FluentCaller;
use App\Traits\HasDynamicAnswers;
use Illuminate\Support\Collection;

class QuestionValuable
{
    use FluentCaller, HasDynamicAnswers;

    public Cooperation $cooperation;
    public array $questionValues;

    public function __construct(Cooperation $cooperation, array $questionValues, Collection $answers)
    {
        $this->cooperation = $cooperation;
        $this->questionValues = $questionValues;
        $this->answers = $answers;
    }
}