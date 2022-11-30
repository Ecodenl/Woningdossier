<?php

namespace App\Helpers\Conditions\Evaluators;

use App\Models\InputSource;
use App\Models\Building;
use App\Traits\FluentCaller;
use App\Traits\HasDynamicAnswers;
use Illuminate\Support\Collection;

abstract class ShouldEvaluate
{
    use FluentCaller,
        HasDynamicAnswers;

    protected array $override = [];

    public function __construct(Building $building, InputSource $inputSource, ?Collection $answers = null)
    {
        $this->building = $building;
        $this->inputSource = $inputSource;
        $this->answers = $answers;
    }

    public function override($override): self
    {
        $this->override = $override;
        return $this;
    }

    abstract public function evaluate($value = null): array;
}