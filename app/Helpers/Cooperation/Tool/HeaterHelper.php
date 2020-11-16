<?php

namespace App\Helpers\Cooperation\Tool;

use App\Calculations\Heater;
use App\Calculations\SolarPanel;
use App\Events\StepCleared;
use App\Helpers\HoomdossierSession;
use App\Models\Building;
use App\Models\BuildingElement;
use App\Models\BuildingFeature;
use App\Models\BuildingHeater;
use App\Models\Element;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\Step;
use App\Models\UserActionPlanAdvice;
use App\Models\UserEnergyHabit;
use App\Scopes\GetValueScope;
use App\Services\UserActionPlanAdviceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HeaterHelper extends ToolHelper
{
    public function saveValues(): ToolHelper
    {
        BuildingHeater::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
            [
                'building_id' => $this->building->id,
                'input_source_id' => $this->inputSource->id
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
        $userInterestsForHeater = $this
            ->user
            ->userInterestsForSpecificType(Step::class, Step::findByShort('heater')->id, $this->inputSource)
            ->first();

        $this->setValues([
            'building_heaters' => [
                $buildingHeater instanceof BuildingHeater ? $buildingHeater->toArray() : [],
            ],
            'user_energy_habits' => [
                'water_comfort_id' => $userEnergyHabit->water_comfort_id ?? null,
            ],
            'user_interests' => [
                'interested_in_id' => optional($userInterestsForHeater)->interested_in_id,
                'interest_id' => optional($userInterestsForHeater)->interest_id,
            ],
        ]);
        return $this;
    }

    public function createAdvices(): ToolHelper
    {
        $step = Step::findByShort('heater');

        $results = Heater::calculate($this->building, $this->user->energyHabit, $this->getValues());

        // remove old results
        UserActionPlanAdviceService::clearForStep($this->user, $this->inputSource, $step);

        if (isset($results['cost_indication']) && $results['cost_indication'] > 0) {
            $measureApplication = MeasureApplication::where('short', 'heater-place-replace')->first();
            if ($measureApplication instanceof MeasureApplication) {
                $actionPlanAdvice = new UserActionPlanAdvice($results);
                $actionPlanAdvice->costs = $results['cost_indication']; // only outlier
                $actionPlanAdvice->user()->associate($this->user);
                $actionPlanAdvice->measureApplication()->associate($measureApplication);
                $actionPlanAdvice->step()->associate($step);
                $actionPlanAdvice->save();
            }
        }
        return $this;
    }

    /**
     * Method to clear the building feature data for wall insulation step.
     *
     * @param Building $tbuilding
     * @param InputSource $inputSource
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