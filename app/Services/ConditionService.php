<?php

namespace App\Services;

use App\Helpers\Conditions\ConditionEvaluator;
use App\Models\Alert;
use App\Models\Building;
use App\Models\InputSource;
use App\Models\SubStep;
use App\Models\SubSteppable;
use App\Models\ToolQuestionCustomValue;
use App\Models\ToolQuestionValuable;
use App\Traits\FluentCaller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ConditionService
{
    use FluentCaller;

    protected Model $model;
    protected Building $building;
    protected InputSource $inputSource;

    public function forModel(Model $model): self
    {
        // TODO: Should we check for supported classes or just expect the developers to not be retarded?
        $this->model = $model;
        return $this;
    }

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

    public function isViewable(?Collection $answers = null): bool
    {
        $answers = is_null($answers) ? collect(): $answers;

        $selfConditionsOnly = [
            SubStep::class, ToolQuestionCustomValue::class, ToolQuestionValuable::class, Alert::class,
        ];

        $class = get_class($this->model);
        $evaluator = ConditionEvaluator::init()
            ->building($this->building)
            ->inputSource($this->inputSource);

        if (in_array($class, $selfConditionsOnly)) {
            $conditions = $this->model->conditions ?? [];
        } elseif ($class === SubSteppable::class) {
            $conditions = $this->model->conditions ?? [];
            $conditions = array_merge($conditions, $this->model->subStep->conditions ?? []);
        } else {
            // So it isn't a simple case. We will have to iterate. In this case it's a ToolQuestion or
            // ToolCalculationResult. We fetch all SubSteps and SubSteppables that have conditions to create one
            // giant conditional array.
            $conditions = [];

            $subSteps = $this->model->subSteps()
                ->where('sub_steps.conditions', '!=', null)
                ->where('sub_steps.conditions', '!=', DB::raw("cast('[]' as json)"))
                ->get();

            $subSteppables = $this->model->subSteps()
                ->wherePivot('conditions', '!=', null)
                ->wherePivot('conditions', '!=', DB::raw("cast('[]' as json)"))
                ->get();

            foreach ($subSteps as $subStep) {
                $conditions = array_merge($conditions, $subStep->conditions);
            }
            foreach ($subSteppables as $subSteppable) {
                $conditions = array_merge($conditions, $subSteppable->pivot->conditions);
            }

            // In the scenario that NONE of the SubSteppables or SubSteps have conditions, they will remain empty, and
            // evaluation will pass
        }

        $answers = $evaluator->getToolAnswersForConditions($conditions)->merge($answers);

        return $evaluator->evaluateCollection($conditions, $answers);
    }
}