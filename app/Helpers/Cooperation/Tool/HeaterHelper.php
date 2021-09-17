<?php

namespace App\Helpers\Cooperation\Tool;

use App\Calculations\Heater;
use App\Events\StepCleared;
use App\Models\Building;
use App\Models\BuildingHeater;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\Step;
use App\Models\UserActionPlanAdvice;
use App\Scopes\GetValueScope;
use App\Services\UserActionPlanAdviceService;

class HeaterHelper extends ToolHelper
{
    public function saveValues(): ToolHelper
    {
        BuildingHeater::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
            [
                'building_id' => $this->building->id,
                'input_source_id' => $this->inputSource->id,
            ],
            $this->getValues('building_heaters')
        );

        $this->building
            ->user
            ->energyHabit()
            ->forInputSource($this->inputSource)
            ->update($this->getValues('user_energy_habits'));

        return $this;
    }

    public function createValues(): ToolHelper
    {
        $buildingHeater = $this->building->heater()->forInputSource($this->inputSource)->first();
        $userEnergyHabit = $this->user->energyHabit()->forInputSource($this->inputSource)->first();

        $this->setValues([
            'building_heaters' => $buildingHeater instanceof BuildingHeater ? $buildingHeater->toArray() : [],
            'user_energy_habits' => [
                'water_comfort_id' => $userEnergyHabit->water_comfort_id ?? null,
            ],
        ]);

        return $this;
    }

    public function createAdvices(): ToolHelper
    {
        $step = Step::findByShort('heater');

        $userEnergyHabit = $this->user->energyHabit()->forInputSource($this->inputSource)->first();
        $results = Heater::calculate($this->building, $userEnergyHabit, $this->getValues());

        $oldAdvices = UserActionPlanAdviceService::clearForStep($this->user, $this->inputSource, $step);

        if (isset($results['cost_indication']) && $results['cost_indication'] > 0) {
            $measureApplication = MeasureApplication::where('short', 'heater-place-replace')->first();
            if ($measureApplication instanceof MeasureApplication) {
                $actionPlanAdvice = new UserActionPlanAdvice($results);
                $actionPlanAdvice->costs = ['from' => $results['cost_indication']]; // only outlier
                $actionPlanAdvice->input_source_id = $this->inputSource->id;
                $actionPlanAdvice->user()->associate($this->user);
                $actionPlanAdvice->userActionPlanAdvisable()->associate($measureApplication);
                $actionPlanAdvice->step()->associate($step);

                UserActionPlanAdviceService::checkOldAdvices($actionPlanAdvice, $measureApplication, $oldAdvices);

                $actionPlanAdvice->save();
            }
        }

        return $this;
    }

    /**
     * Method to clear the building feature data for wall insulation step.
     */
    public static function clear(Building $building, InputSource $inputSource)
    {
        $building->heater()->forInputSource($inputSource)->delete();

        $building
            ->user
            ->energyHabit()
            ->withoutGlobalScope(GetValueScope::class)
            ->update([
                'water_comfort_id' => null,
            ]);

        StepCleared::dispatch($building->user, $inputSource, Step::findByShort('heater'));
    }
}
