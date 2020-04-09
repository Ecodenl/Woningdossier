<?php

namespace App\Helpers\Cooperation\Tool;

use App\Events\StepCleared;
use App\Models\Building;
use App\Models\BuildingElement;
use App\Models\BuildingFeature;
use App\Models\BuildingHeater;
use App\Models\Element;
use App\Models\InputSource;
use App\Models\Step;
use App\Models\UserEnergyHabit;
use App\Scopes\GetValueScope;

class HeaterHelper
{

    /**
     * Method to clear all the saved data for the step, except for the comments.
     *
     * @param Building $building
     * @param InputSource $inputSource
     * @param array $buildingFeatureData
     * @param array $buildingElementData
     */
    public static function save(Building $building, InputSource $inputSource, array $saveData)
    {
        BuildingHeater::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
            [
                'building_id' => $building->id,
                'input_source_id' => $building->id,
            ],
            $saveData['building_heaters']
        );

        $building
            ->user
            ->energyHabit()
            ->withoutGlobalScope(GetValueScope::class)
            ->update($saveData['user_energy_habits']);
    }

    /**
     * Method to clear the building feature data for wall insulation step.
     *
     * @param Building $building
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