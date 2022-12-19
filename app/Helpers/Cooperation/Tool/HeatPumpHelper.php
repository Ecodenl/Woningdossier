<?php

namespace App\Helpers\Cooperation\Tool;

use App\Calculations\HeatPump;
use App\Helpers\Conditions\ConditionEvaluator;
use App\Models\MeasureApplication;
use App\Models\ServiceValue;
use App\Models\Step;
use App\Models\SubStep;
use App\Models\ToolQuestion;
use App\Models\UserActionPlanAdvice;
use App\Services\ConditionService;
use App\Services\UserActionPlanAdviceService;

class HeatPumpHelper extends ToolHelper
{
    const MEASURE_SERVICE_LINK = [
        'hybrid-heat-pump-outside-air' => 1,
        'hybrid-heat-pump-ventilation-air' => 2,
        'hybrid-heat-pump-pvt-panels' => 3,
        'full-heat-pump-outside-air' => 4,
        'full-heat-pump-ground-heat' => 5,
        'full-heat-pump-pvt-panels' => 6,
    ];

    public function saveValues(): ToolHelper
    {
        // Format isn't applicable for this helper, but it is required due to abstraction
        return $this;
    }

    public function createValues(): ToolHelper
    {
        // Technically not correct but it makes it easier to reuse considerable for the heat pump boiler
        $heatPump = Step::findByShort('heat-pump');
        $heating = Step::findByShort('heating');

        $this->setValues([
            'considerables' => [
                $heatPump->id => [
                    'is_considering' => $this->considersByConditions(
                        $this->getConditionConsiderable('heat-pump')
                    ),
                ],
                $heating->id => [
                    'is_considering' => $this->considersByConditions(
                        $this->getConditionConsiderable('heat-pump-boiler')
                    ),
                ],
            ],
            'has_completed_expert' => ConditionService::init()->building($this->building)->inputSource($this->inputSource)->hasCompletedSteps(['heating']),
            'updated_measure_ids' => [],
        ]);

        return $this;
    }

    public function createAdvices(): ToolHelper
    {
        $updatedMeasureIds = $this->getValues('updated_measure_ids');

        $step = Step::findByShort('heat-pump');
        $oldAdvices = UserActionPlanAdviceService::clearForStep($this->user, $this->inputSource, $step);

        // Prepare: Tons of logic to define heat pump measures... (scroll further for heat pump boiler)
        if ($this->considers($step)) {
            $heatPumpToCalculate = null;
            $currentCalculateValue = null;

            $evaluator = ConditionEvaluator::init()
                ->building($this->building)
                ->inputSource($this->inputSource);

            if ($this->getValues('has_completed_expert')) {
                // User has finished expert step, so we will use the expert logic
                $newType = \App\Deprecation\ToolHelper::getServiceValueByCustomValue(
                    'heat-pump',
                    'new-heat-pump-type',
                    $this->getAnswer('new-heat-pump-type'),
                );

                if ($newType instanceof ServiceValue) {
                    $short = array_flip(static::MEASURE_SERVICE_LINK)[$newType->calculate_value];
                    $heatPumpToCalculate = $short;
                }
            } else {
                // User has not yet finished the expert step. Let's check if they are interested, else fall back
                // to a potential current

                // If we're not in expert yet, we need the heating temp for the calculations
                $heatingTemp = $this->getAnswer('boiler-setting-comfort-heat');
                // We assume the worst if the user doesn't know
                $heatingTemp = $heatingTemp === 'unsure' ? 'temp-high' : $heatingTemp;

                $heatPumpInterestSubStep = SubStep::bySlug('warmtepomp-interesse')->first();
                $interested = $this->getAnswer('interested-in-heat-pump') === 'yes';

                // If they can answer the interest, and are interested, we will look at their variant interest
                if ($evaluator->evaluate($heatPumpInterestSubStep->conditions) && $interested) {
                    $interest = $this->getAnswer('interested-in-heat-pump-variant');

                    if ($interest === 'full-heat-pump') {
                        $heatPumpToCalculate = 'full-heat-pump-outside-air';
                    } elseif ($interest === 'hybrid-heat-pump') {
                        $heatPumpToCalculate = 'hybrid-heat-pump-outside-air';
                    } else {
                        // If they use low temp, we suggest a full heat pump. Otherwise we always suggest hybrid.
                        // In the case the user is unsure about their temp usage, as mentioned above, we assume the
                        // worst case and thus also suggest hybrid
                        $heatPumpToCalculate = $heatingTemp === 'temp-low' ? 'full-heat-pump-outside-air'
                            : 'hybrid-heat-pump-outside-air';
                    }
                }
            }

            $heatPumpSubStep = SubStep::bySlug('warmtepomp')->first();
            if ($evaluator->evaluate($heatPumpSubStep->conditions)) {
                $currentCalculateValue = $this->getCurrentHeatPump();

                // No heat pump to calculate, so we will fall back to the one they already have
                if (is_null($heatPumpToCalculate) && ! is_null($currentCalculateValue) && ! $this->getValues('has_completed_expert')) {
                    $short = array_flip(static::MEASURE_SERVICE_LINK)[$currentCalculateValue];
                    $heatPumpToCalculate = $short;
                }
            }

            if (! is_null($heatPumpToCalculate)) {
                $results = $this->getResults($heatPumpToCalculate, $currentCalculateValue);

                $measureApplication = MeasureApplication::findByShort($heatPumpToCalculate);
                if ($measureApplication instanceof MeasureApplication) {
                    $actionPlanAdvice = new UserActionPlanAdvice($results);
                    $actionPlanAdvice->costs = UserActionPlanAdviceService::formatCosts($results['cost_indication']);
                    $actionPlanAdvice->savings_money = $results['savings_money'];
                    $actionPlanAdvice->input_source_id = $this->inputSource->id;
                    $actionPlanAdvice->user()->associate($this->user);
                    $actionPlanAdvice->userActionPlanAdvisable()->associate($measureApplication);
                    $actionPlanAdvice->step()->associate($step);

                    // We only want to check old advices if the updated attributes are not relevant to this measure
                    if (! in_array($measureApplication->id, $updatedMeasureIds) && $this->shouldCheckOldAdvices()) {
                        UserActionPlanAdviceService::checkOldAdvices($actionPlanAdvice, $measureApplication,
                            $oldAdvices);
                    }

                    $actionPlanAdvice->save();
                }
            }
        }

        // We don't need to check anything else; there's only one type, so if the user has it, that's it
        if ($this->considers(Step::findByShort('heating'))) {
            $measureApplication = MeasureApplication::findByShort('heat-pump-boiler-place-replace');
            if ($measureApplication instanceof MeasureApplication) {
                // TODO: Values!
                $actionPlanAdvice = new UserActionPlanAdvice();
                //$actionPlanAdvice->costs = UserActionPlanAdviceService::formatCosts($results['cost_indication']);
                $actionPlanAdvice->input_source_id = $this->inputSource->id;
                $actionPlanAdvice->user()->associate($this->user);
                $actionPlanAdvice->userActionPlanAdvisable()->associate($measureApplication);
                $actionPlanAdvice->step()->associate($step);

                // We only want to check old advices if the updated attributes are not relevant to this measure
                if (! in_array($measureApplication->id, $updatedMeasureIds) && $this->shouldCheckOldAdvices()) {
                    UserActionPlanAdviceService::checkOldAdvices($actionPlanAdvice, $measureApplication,
                        $oldAdvices);
                }

                $actionPlanAdvice->save();
            }
        }

        return $this;
    }

    public function getCurrentHeatPump(): ?int
    {
        $type = ServiceValue::find($this->getAnswer('heat-pump-type'));

        return $type instanceof ServiceValue ? $type->calculate_value : null;
    }

    public function getResults(string $heatPumpToCalculate, ?int $currentCalculateValue = null): array
    {
        $calculateValue = static::MEASURE_SERVICE_LINK[$heatPumpToCalculate];

        // Ensure we correctly "spoof" the calculator values
        $answers = $this->getValues();
        $answers['new-heat-pump-type'] = ToolQuestion::findByShort('new-heat-pump-type')
            ->toolQuestionCustomValues()
            ->where('extra->calculate_value', $calculateValue)
            ->first()->short;

        if (! $this->getValues('has_completed_expert')) {
            // Force 0 to have the desired power calculated.
            $answers['heat-pump-preferred-power'] = 0;
        }

        $results = HeatPump::calculate($this->building, $this->inputSource, collect($answers));

        // We need to check the current type; if the placed date surpasses maintenance time, we will
        // set savings to 0 since the measure will then qualify as a replace of the current type.
        if (! is_null($currentCalculateValue) && $currentCalculateValue === $calculateValue) {
            // Type exists, and this iteration is that type. We don't need to evaluate a second time, since
            // by the fact we have the current type, we already know the user was able to answer the question.
            $placeYear = $this->getAnswer('heat-pump-placed-date');

            if (is_numeric($placeYear)) {
                $diff = now()->format('Y') - $placeYear;

                // It's too old, so we set savings to 0
                if ($diff >= 18) {
                    $results['savings_money'] = 0;
                }
            }
        }

        return $results;
    }
}