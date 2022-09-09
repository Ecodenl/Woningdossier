<?php

namespace App\Helpers\Cooperation\Tool;

use App\Calculations\HeatPump;
use App\Models\MeasureApplication;
use App\Models\Service;
use App\Models\ServiceValue;
use App\Models\Step;
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
        $comfortHeat = ToolQuestion::findByShort('new-boiler-setting-comfort-heat');

        $this->setValues([
            'considerables' => [
                $step->id => [
                    'is_considering' => $this->considersByAnswer('heat-source-considerable', 'heat-pump'),
                ],
            ],
            'boiler' => ServiceValue::find(
                $this->building->getAnswer($this->masterInputSource, ToolQuestion::findByShort('new-boiler-type')),
            ),
            'heatingTemperature' => $comfortHeat->toolQuestionCustomValues()->whereShort(
                $this->building->getAnswer($this->masterInputSource, $comfortHeat),
            ),
            'desiredPower' => $this->building->getAnswer($this->masterInputSource,
                ToolQuestion::findByShort('heat-pump-preferred-power')),
            'updated_measure_ids' => [],
        ]);

        return $this;
    }

    public function createAdvices(): ToolHelper
    {
        $updatedMeasureIds = $this->getValues('updated_measure_ids');

        $step = Step::findByShort('heat-pump');

        $oldAdvices = UserActionPlanAdviceService::clearForStep($this->user, $this->inputSource, $step);

        if ($this->considers($step)) {
            $userEnergyHabit = $this->user->energyHabit()->forInputSource($this->inputSource)->first();
            $heatPumpService = Service::findByShort('heat-pump');

            foreach (static::MEASURE_SERVICE_LINK as $measureShort => $calculateValue) {
                $serviceValue = $heatPumpService->values()->where('calculate_value', $calculateValue)->first();
                $calculateData = $this->getValues();
                $calculateData['heatPumpConfigurable'] = $serviceValue;
                $results = HeatPump::calculate($this->building, $this->inputSource, $userEnergyHabit, $calculateData);

                if (isset($results['cost_indication']) && $results['cost_indication'] > 0) {
                    $measureApplication = MeasureApplication::findByShort($measureShort);
                    if ($measureApplication instanceof MeasureApplication) {
                        $actionPlanAdvice = new UserActionPlanAdvice($results);
                        $actionPlanAdvice->costs = UserActionPlanAdviceService::formatCosts($results['cost_indication']);
                        $actionPlanAdvice->input_source_id = $this->inputSource->id;
                        $actionPlanAdvice->user()->associate($this->user);
                        $actionPlanAdvice->userActionPlanAdvisable()->associate($measureApplication);
                        $actionPlanAdvice->step()->associate($step);

                        // We only want to check old advices if the updated attributes are not relevant to this measure
                        if (! in_array($measureApplication->id, $updatedMeasureIds) && $this->shouldCheckOldAdvices()) {
                            UserActionPlanAdviceService::checkOldAdvices($actionPlanAdvice, $measureApplication, $oldAdvices);
                        }

                        $actionPlanAdvice->save();
                    }
                }
            }
        }

        return $this;
    }
}
