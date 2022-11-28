<?php

namespace App\Helpers\Conditions\Evaluators;

use App\Models\InputSource;
use App\Models\Building;
use App\Traits\FluentCaller;
use Illuminate\Support\Collection;

abstract class ShouldEvaluate
{
    use FluentCaller;

    protected Building $building;
    protected InputSource $inputSource;
    protected $override = null;

    public function __construct(Building $building, InputSource $inputSource)
    {
        $this->building = $building;
        $this->inputSource = $inputSource;
    }

    public function override($override): self
    {
        $this->override = $override;
        return $this;
    }

    abstract public function evaluate($value = null, ?Collection $answers = null): array;
}