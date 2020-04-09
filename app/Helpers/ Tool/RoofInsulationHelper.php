<?php

namespace App\Helpers\Cooperation\Tool;

use App\Calculations\RoofInsulation as RoofInsulationCalculate;
use App\Events\StepCleared;
use App\Helpers\Calculator;
use App\Helpers\HoomdossierSession;
use App\Helpers\RoofInsulation;
use App\Helpers\RoofInsulationCalculator;
use App\Models\Building;
use App\Models\BuildingFeature;
use App\Models\BuildingRoofType;
use App\Models\InputSource;
use App\Models\Interest;
use App\Models\MeasureApplication;
use App\Models\RoofTileStatus;
use App\Models\RoofType;
use App\Models\Step;
use App\Models\UserActionPlanAdvice;
use App\Scopes\GetValueScope;
use App\Services\ModelService;
use App\Services\UserActionPlanAdviceService;
use Carbon\Carbon;

class RoofInsulationHelper
{
    /**
     * Save the advices from the save data to the user action plan advices
     *
     * @param Building $building
     * @param InputSource $inputSource
     * @param array $saveData
     * @throws \Exception
     */
    public static function saveAdvices(Building $building, InputSource $inputSource, array $saveData)
    {
        $results = RoofInsulationCalculate::calculate($building, $inputSource, $building->user->energyHabit, $saveData);

        $result = [];

        $user = $building->user;
        
        $step = Step::findByShort('roof-insulation');

        $buildingRoofTypeData = $saveData['building_roof_types'];
        // Remove old results
        UserActionPlanAdviceService::clearForStep($user, $inputSource, $step);

        $roofTypeIds = $saveData['building_roof_type_ids'];
        foreach ($roofTypeIds as $roofTypeId) {
            $roofType = RoofType::findOrFail($roofTypeId);
            if ($roofType instanceof RoofType) {
                $cat = RoofInsulation::getRoofTypeCategory($roofType);
                // add as key to result array
                $result[$cat] = [
                    'type' => RoofInsulation::getRoofTypeSubCategory($roofType),
                ];
            }
        }

        foreach (array_keys($result) as $roofCat) {
            $isBitumenOnPitchedRoof = 'pitched' == $roofCat && 'bitumen' == $results['pitched']['type'];
            // It's a bitumen roof is the category is not pitched or none (so currently only: flat)
            $isBitumenRoof = ! in_array($roofCat, ['none', 'pitched']) || $isBitumenOnPitchedRoof;

            $measureApplicationId = $buildingRoofTypeData[$roofCat]['extra']['measure_application_id'];
            if ($measureApplicationId > 0) {
                // results in an advice
                $measureApplication = MeasureApplication::find($measureApplicationId);
                if ($measureApplication instanceof MeasureApplication) {
                    $actionPlanAdvice = null;

                    $interest = Interest::find($saveData['user_interests']['interest_id']);

                    if (1 == $interest->calculate_value) {
                        // on short term: this year
                        $advicedYear = Carbon::now()->year;
                    } elseif (2 == $interest->calculate_value) {
                        // on term: this year + 5
                        $advicedYear = Carbon::now()->year + 5;
                    } else {
                        $advicedYear = $results[$roofCat]['replace']['year'];
                    }

                    if (isset($results[$roofCat]['cost_indication']) && $results[$roofCat]['cost_indication'] > 0) {
                        // take the array $roofCat array
                        $actionPlanAdvice = new UserActionPlanAdvice($results[$roofCat]);
                        $actionPlanAdvice->year = $advicedYear;
                        $actionPlanAdvice->costs = $results[$roofCat]['cost_indication'];
                    }

                    if ($actionPlanAdvice instanceof UserActionPlanAdvice) {
                        $actionPlanAdvice->user()->associate($user);
                        $actionPlanAdvice->measureApplication()->associate($measureApplication);
                        $actionPlanAdvice->step()->associate($step);
                        $actionPlanAdvice->save();
                    }
                }
            }
            $extra = $buildingRoofTypeData[$roofCat]['extra'] ?? [];
            if (array_key_exists('zinc_replaced_date', $extra)) {
                $zincReplaceYear = (int) $extra['zinc_replaced_date'];
                // todo Get surface for $roofCat from building_roof_types (or elsewhere) for this input source
                // Default: get from building_roof_types table for this input source
                $roofType = RoofType::where('short', '=', $roofCat)->first();

                $zincSurface = 0;
                if ($roofType instanceof RoofType) {
                    $buildingRoofType = $building->roofTypes()->where('roof_type_id', '=', $roofType->id)->first();
                    if ($buildingRoofType instanceof BuildingRoofType) {
                        $zincSurface = $buildingRoofType->zinc_surface;
                    }
                }
                // Note there's no such request input just yet. We're not sure this will be available for the user
                // to fill in.
                $zincSurface = $request->input('building_roof_types.'.$roofCat.'.zinc_surface', $zincSurface);

                if ($zincReplaceYear > 0 && $zincSurface > 0) {
                    /** @var MeasureApplication $zincReplaceMeasure */
                    $zincReplaceMeasure = MeasureApplication::where('short', 'replace-zinc-'.$roofCat)->first();

                    $year = RoofInsulationCalculator::determineApplicationYear($zincReplaceMeasure, $zincReplaceYear, 1);
                    $costs = Calculator::calculateMeasureApplicationCosts($zincReplaceMeasure, $zincSurface, $year, false);

                    $actionPlanAdvice = new UserActionPlanAdvice(compact('costs', 'year'));
                    $actionPlanAdvice->user()->associate($user);
                    $actionPlanAdvice->measureApplication()->associate($zincReplaceMeasure);
                    $actionPlanAdvice->step()->associate($step);
                    $actionPlanAdvice->save();
                }
            }
            if (array_key_exists('tiles_condition', $extra)) {
                $tilesCondition = (int) $extra['tiles_condition'];
                $surface = $request->input('building_roof_types.'.$roofCat.'.roof_surface', 0);
                if ($tilesCondition > 0 && $surface > 0) {
                    $replaceMeasure = MeasureApplication::where('short', 'replace-tiles')->first();
                    // no year here. Default is this year. It is incremented by factor * maintenance years
                    $year = Carbon::now()->year;
                    $roofTilesStatus = RoofTileStatus::find($tilesCondition);

                    if ($roofTilesStatus instanceof RoofTileStatus) {
                        $factor = ($roofTilesStatus->calculate_value / 100);

                        $year = RoofInsulationCalculator::determineApplicationYear($replaceMeasure, $year, $factor);
                        $costs = Calculator::calculateMeasureApplicationCosts($replaceMeasure, $surface, $year, false);

                        $actionPlanAdvice = new UserActionPlanAdvice(compact('costs', 'year'));
                        $actionPlanAdvice->user()->associate($user);
                        $actionPlanAdvice->measureApplication()->associate($replaceMeasure);
                        $actionPlanAdvice->step()->associate($step);
                        $actionPlanAdvice->save();
                    }
                }
            }
            if ($isBitumenRoof && array_key_exists('bitumen_replaced_date', $extra)) {
                $bitumenReplaceYear = (int) $extra['bitumen_replaced_date'];
                if ($bitumenReplaceYear <= 0) {
                    $bitumenReplaceYear = Carbon::now()->year - 10;
                }
                $surface = $request->input('building_roof_types.'.$roofCat.'.roof_surface', 0);

                if ($bitumenReplaceYear > 0 && $surface > 0) {
                    $replaceMeasure = MeasureApplication::where('short', 'replace-roof-insulation')->first();
                    // no percentages here. We just do this to keep the determineApplicationYear definition in one place
                    $year = $bitumenReplaceYear;
                    $factor = 1;

                    $year = RoofInsulationCalculator::determineApplicationYear($replaceMeasure, $year, $factor);
                    $costs = Calculator::calculateMeasureApplicationCosts($replaceMeasure, $surface, $year, false);

                    $actionPlanAdvice = new UserActionPlanAdvice(compact('costs', 'year'));
                    $actionPlanAdvice->user()->associate($user);
                    $actionPlanAdvice->measureApplication()->associate($replaceMeasure);
                    $actionPlanAdvice->step()->associate($step);
                    $actionPlanAdvice->save();
                }
            }
        }
    }

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
        $buildingFeatureData = $saveData['building_features'];
        // the selected roof types for the current situation
        // get the selected roof type ids
        $roofTypeIds = $saveData['building_roof_type_ids'];
        $buildingRoofTypeData = $saveData['building_roof_types'];


        foreach ($roofTypeIds as $roofTypeId) {
            $roofType = RoofType::findOrFail($roofTypeId);
            if ($roofType instanceof RoofType) {

                $buildingRoofType = $building->roofTypes()->where('roof_type_id', '=', $roofType->id)->first();
                $zincSurface = $buildingRoofType instanceof BuildingRoofType ? $buildingRoofType->zinc_surface : 0;

                // Note there's no such request input just yet. We're not sure this will be available for the user
                // to fill in.
                $buildingRoofTypeData[$roofType->short]['zinc_surface'] = $zincSurface;
                $buildingRoofTypeData[$roofType->short]['roof_type_id'] = $roofType->id;
            }
        }

        BuildingFeature::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
            [
                'building_id' => $building->id,
                'input_source_id' => $inputSource->id,
            ],
            $buildingFeatureData
        );

        // we dont know which roof_type_id we will get, so we delete all the rows and create new ones.
        ModelService::deleteAndCreate(BuildingRoofType::class,
            [
                'building_id' => $building->id,
                'input_source_id' => $inputSource->id,
            ],
            $buildingRoofTypeData
        );

        self::saveAdvices($building, $inputSource, $saveData);
    }

    /**
     * Method to clear the building feature data for wall insulation step.
     *
     * @param Building $building
     * @param InputSource $inputSource
     */
    public static function clear(Building $building, InputSource $inputSource)
    {
        BuildingFeature::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
            [
                'building_id' => $building->id,
                'input_source_id' => $inputSource->id,
            ],
            [
                'roof_type_id' => null
            ]
        );

        // delete my own building roof types.
        BuildingRoofType::forMe($building->user)->forInputSource($inputSource)->delete();

        StepCleared::dispatch($building->user, $inputSource, Step::findByShort('roof-insulation'));
    }
}