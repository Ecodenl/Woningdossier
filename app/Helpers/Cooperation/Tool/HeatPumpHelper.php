<?php

namespace App\Helpers\Cooperation\Tool;

use App\Calculations\HeatPump;
use App\Helpers\Conditions\ConditionEvaluator;
use App\Models\MeasureApplication;
use App\Models\Service;
use App\Models\ServiceValue;
use App\Models\Step;
use App\Models\SubStep;
use App\Models\ToolQuestion;
use App\Models\UserActionPlanAdvice;
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
        $step = Step::findByShort('heat-pump');

        $this->setValues([
            'considerables' => [
                $step->id => [
                    'is_considering' => $this->considersByAnswer('heat-source-considerable', 'heat-pump'),
                ],
            ],
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
            $userEnergyHabit = $this->user->energyHabit()->forInputSource($this->inputSource)->first();
            $heatPumpsToCalculate = [];
            $currentCalculateValue = null;

            $evaluator = ConditionEvaluator::init()
                ->building($this->building)
                ->inputSource($this->inputSource);

            // First check if the user has a heat pump currently
            $heatPumpSubStep = SubStep::bySlug('warmtepomp')->first();
            if ($evaluator->evaluate($heatPumpSubStep->conditions)) {
                $type = ServiceValue::find($this->getAnswer('heat-pump-type'));

                if ($type instanceof ServiceValue) {
                    $short = array_flip(static::MEASURE_SERVICE_LINK)[$type->calculate_value];
                    $heatPumpsToCalculate[] = $short;
                    $currentCalculateValue = $type->calculate_value;
                }
            }

            // Now check if they have interest / already selected which heat pump they wants
            $newType = Service::findByShort('heat-pump')->values()
                ->where(
                    'calculate_value',
                    ToolQuestion::findByShort('new-heat-pump-type')->toolQuestionCustomValues()
                        ->whereShort($this->getAnswer('new-heat-pump-type'))->first()->extra['calculate_value'] ?? null
                )->first();

            if ($newType instanceof ServiceValue) {
                $short = array_flip(static::MEASURE_SERVICE_LINK)[$newType->calculate_value];
                $heatPumpsToCalculate[] = $short;
            } else {
                // No new type selected, so the user has not yet answered the expert page. We will look at their
                // interest.
                $heatPumpInterestSubStep = SubStep::bySlug('warmtepomp-interesse')->first();
                $interested = $this->getAnswer('interested-in-heat-pump') === 'yes';

                // If they can answer the interest, and are interested, we will look at their variant interest
                if ($evaluator->evaluate($heatPumpInterestSubStep->conditions) && $interested) {
                    $interest = $this->getAnswer('interested-in-heat-pump-variant');

                    if ($interest === 'full-heat-pump') {
                        $heatPumpsToCalculate[] = 'full-heat-pump-outside-air';
                    } elseif ($interest === 'hybrid-heat-pump') {
                        $heatPumpsToCalculate[] = 'hybrid-heat-pump-outside-air';
                    } else {
                        // If they want advise, we advise based on heating temperature
                        $temp = $this->getAnswer('boiler-setting-comfort-heat');

                        // If they use low temp, we suggest a full heat pump. Otherwise we always suggest hybrid.
                        // In the case the user is unsure about their temp usage, we assume the worst case and thus
                        // also suggest hybrid
                        $heatPumpsToCalculate[] = $temp === 'temp-low' ? 'full-heat-pump-outside-air'
                            : 'hybrid-heat-pump-outside-air';
                    }
                }
            }

            foreach ($heatPumpsToCalculate as $measureShort) {
                $calculateValue = static::MEASURE_SERVICE_LINK[$measureShort];

                $answers = $this->getValues();
                $answers['new-heat-pump-type'] = ToolQuestion::findByShort('new-heat-pump-type')
                    ->toolQuestionCustomValues()
                    ->where('extra->calculate_value', $calculateValue)
                    ->first()->short;
                $results = HeatPump::calculate($this->building, $this->inputSource, $userEnergyHabit,
                    collect($answers));

                $savingsMoney = null;

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
                            $savingsMoney = 0;
                        }
                    }
                }

                if (isset($results['cost_indication']) && $results['cost_indication'] > 0) {
                    $measureApplication = MeasureApplication::findByShort($measureShort);
                    if ($measureApplication instanceof MeasureApplication) {
                        $actionPlanAdvice = new UserActionPlanAdvice($results);
                        $actionPlanAdvice->costs = UserActionPlanAdviceService::formatCosts($results['cost_indication']);
                        $actionPlanAdvice->savings_money = is_null($savingsMoney) ? $results['savings_money'] : $savingsMoney;
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
        }

        $heatSourceWaterAnswers = array_merge(
            $this->getAnswer('heat-source-warm-tap-water'),
            $this->getAnswer('new-heat-source-warm-tap-water')
        );

        // The user uses a heat pump boiler or wants one so we provide the measure application
        if (in_array('heat-pump-boiler', $heatSourceWaterAnswers)) {
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
}