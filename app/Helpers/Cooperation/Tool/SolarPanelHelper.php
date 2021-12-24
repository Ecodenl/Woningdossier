<?php

namespace App\Helpers\Cooperation\Tool;

use App\Calculations\SolarPanel;
use App\Events\StepCleared;
use App\Models\Building;
use App\Models\BuildingElement;
use App\Models\BuildingPvPanel;
use App\Models\BuildingService;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\Service;
use App\Models\Step;
use App\Models\UserActionPlanAdvice;
use App\Scopes\GetValueScope;
use App\Services\UserActionPlanAdviceService;

class SolarPanelHelper extends ToolHelper
{
    public function saveValues(): ToolHelper
    {
        BuildingPvPanel::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
            [
                'building_id' => $this->building->id,
                'input_source_id' => $this->inputSource->id,
            ],
            $this->getValues('building_pv_panels')
        );

        $this->user
            ->energyHabit()
            ->forInputSource($this->inputSource)
            ->first() // Fetch first, else no model events
            ->update($this->getValues('user_energy_habits'));

        $totalSunPanelsService = Service::findByShort('total-sun-panels');

        BuildingService::allInputSources()->updateOrCreate(
            [
                'building_id' => $this->building->id,
                'input_source_id' => $this->inputSource->id,
                'service_id' => $totalSunPanelsService->id,
            ],
            $this->getValues("building_services.{$totalSunPanelsService->id}")
        );


        return $this;
    }

    public function createAdvices(): ToolHelper
    {
        $updatedMeasureIds = $this->getValues('updated_measure_ids');

        $step = Step::findByShort('solar-panels');

        $results = SolarPanel::calculate($this->building, $this->getValues());

        $oldAdvices = UserActionPlanAdviceService::clearForStep($this->user, $this->inputSource, $step);

        if ($this->considers($step) && isset($results['cost_indication']) && $results['cost_indication'] > 0 ) {
            $measureApplication = MeasureApplication::where('short', 'solar-panels-place-replace')->first();
            if ($measureApplication instanceof MeasureApplication) {
                $actionPlanAdvice = new UserActionPlanAdvice($results);
                $actionPlanAdvice->costs = UserActionPlanAdviceService::formatCosts($results['cost_indication']);
                $actionPlanAdvice->input_source_id = $this->inputSource->id;
                $actionPlanAdvice->savings_electricity = $results['yield_electricity'];
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

        return $this;
    }

    public function createValues(): ToolHelper
    {
        $buildingPvPanels = $this->building->pvPanels()->forInputSource($this->masterInputSource)->first();
        $userEnergyHabit = $this->user->energyHabit()->forInputSource($this->masterInputSource)->first();

        $step = Step::findByShort('solar-panels');

        $this->setValues([
            'building_pv_panels' => $buildingPvPanels instanceof BuildingPvPanel ? $buildingPvPanels->toArray() : [],
            'user_energy_habits' => [
                'amount_electricity' => $userEnergyHabit->amount_electricity ?? null,
            ],
            'considerables' => [
                $step->id => [
                    'is_considering' => $this->user->considers($step, $this->masterInputSource),
                ]
            ],
            'updated_measure_ids' => [],
        ]);

        return $this;
    }

    /**
     * Method to clear the pv panels.
     */
    public static function clear(Building $building, InputSource $inputSource)
    {
        BuildingPvPanel::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
            [
                'building_id' => $building->id,
                'input_source_id' => $inputSource->id,
            ],
            [
                'peak_power' => null,
                'number' => null,
                'pv_panel_orientation_id' => null,
                'angle' => null,
            ]
        );

        StepCleared::dispatch($building->user, $inputSource, Step::findByShort('solar-panels'));
    }
}
