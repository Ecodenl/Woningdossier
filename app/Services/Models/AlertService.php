<?php

namespace App\Services\Models;

use App\Helpers\Conditions\ConditionEvaluator;
use App\Models\Alert;
use App\Models\Building;
use App\Models\InputSource;
use App\Traits\FluentCaller;
use Illuminate\Support\Collection;

class AlertService
{
    use FluentCaller;

    const TYPE_MAP = [
        Alert::TYPE_INFO => 'text-blue-900',
        Alert::TYPE_SUCCESS => 'text-green',
        Alert::TYPE_WARNING => 'text-orange',
        Alert::TYPE_DANGER => 'text-red',
    ];

    protected Building $building;
    protected InputSource $inputSource;
    protected ?Collection $answers = null;

    public function building(Building $building): self
    {
        $this->building = $building;
        return $this;
    }

    public function inputSource(InputSource $inputSource): self
    {
        $this->inputSource = $inputSource;
        return $this;
    }

    public function setAnswers(Collection $answers): self
    {
        $this->answers = $answers;
        return $this;
    }

    public function getAlerts(): Collection
    {
        $answers = $this->answers instanceof Collection ? $this->answers : collect();

        $alerts = Alert::all();

        // First fetch all conditions, so we can retrieve any required related answers in one go
        $conditionsForAllAlerts = $alerts->pluck('conditions')->flatten(1)->filter()->all();

        $evaluator = ConditionEvaluator::init()
            ->building($this->building)
            ->inputSource($this->inputSource);

        $evaluator->setAnswers(
            $evaluator->getToolAnswersForConditions($conditionsForAllAlerts, $answers)
        );

        foreach ($alerts as $index => $alert) {
            // Check if we should show this alert
            if (! $evaluator->evaluate($alert->conditions)) {
                $alerts->forget($index);
            }
        }

        return $alerts;
    }
}