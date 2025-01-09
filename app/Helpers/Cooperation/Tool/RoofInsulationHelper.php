<?php

namespace App\Helpers\Cooperation\Tool;

use App\Calculations\RoofInsulation as RoofInsulationCalculate;
use App\Events\StepCleared;
use App\Helpers\RawCalculator;
use App\Helpers\RoofInsulation;
use App\Helpers\RoofInsulationCalculator;
use App\Models\Building;
use App\Models\BuildingFeature;
use App\Models\BuildingRoofType;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\RoofTileStatus;
use App\Models\RoofType;
use App\Models\Step;
use App\Models\UserActionPlanAdvice;
use App\Scopes\GetValueScope;
use App\Services\ModelService;
use App\Services\UserActionPlanAdviceService;
use Carbon\Carbon;

class RoofInsulationHelper extends ToolHelper
{
    public function createAdvices(): ToolHelper
    {
        $updatedMeasureIds = $this->getValues('updated_measure_ids');

        $energyHabit = $this->user->energyHabit()->forInputSource($this->masterInputSource)->first();
        $results = RoofInsulationCalculate::calculate($this->building, $this->masterInputSource, $energyHabit, $this->getValues());

        $result = [];

        $step = Step::findByShort('roof-insulation');

        $buildingRoofTypeData = $this->getValues('building_roof_types');

        $oldAdvices = UserActionPlanAdviceService::clearForStep($this->user, $this->inputSource, $step);

        // Loop all building roof types
        $roofTypeIds = $this->getValues('building_roof_type_ids');
        foreach ($roofTypeIds as $roofTypeId) {
            $roofType = RoofType::findOrFail($roofTypeId);
            // Get category and potential sub category
            if ($roofType instanceof RoofType) {
                $cat = RoofInsulation::getRoofTypeCategory($roofType);
                // add as key to result array
                $result[$cat] = [
                    'type' => RoofInsulation::getRoofTypeSubCategory($roofType),
                ];
            }
        }

        if ($this->considers($step)) {
            foreach (array_keys($result) as $roofCat) {
                $primaryRoof = RoofType::find($this->getValues('building_features')['roof_type_id']);
                $primaryRoofShort = $primaryRoof?->short;

                $roofStep = Step::findByShort('roof-insulation');
                // If the user has answered the step about roof insulation, we will continue with both possible roof
                // categories. However, if they have not, we must ensure that the primary roof matches the category
                if ($this->building->hasAnsweredExpertQuestion($roofStep) ||
                    (! is_null($primaryRoofShort) && $roofCat === RoofType::PRIMARY_TO_SECONDARY_MAP[$primaryRoofShort])
                ) {
                    $isBitumenOnPitchedRoof = 'pitched' == $roofCat && 'bitumen' == $results['pitched']['type'];
                    // It's a bitumen roof is the category is not pitched or none (so currently only: flat)
                    $isBitumenRoof = ! in_array($roofCat, ['none', 'pitched']) || $isBitumenOnPitchedRoof;

                    // when "no roof" is selected there will still be a result, so extra ?? 0
                    $measureApplicationId = $buildingRoofTypeData[$roofCat]['extra']['measure_application_id'] ?? 0;
                    if ($measureApplicationId > 0) {
                        // results in an advice
                        $measureApplication = MeasureApplication::find($measureApplicationId);
                        if ($measureApplication instanceof MeasureApplication) {
                            $actionPlanAdvice = null;

                            $advicedYear = $results[$roofCat]['replace']['year'];

                            if (isset($results[$roofCat]['cost_indication']) && $results[$roofCat]['cost_indication'] > 0) {
                                // take the array $roofCat array
                                $actionPlanAdvice = new UserActionPlanAdvice($results[$roofCat]);
                                $actionPlanAdvice->year = $advicedYear;
                                $actionPlanAdvice->costs = UserActionPlanAdviceService::formatCosts($results[$roofCat]['cost_indication']);
                            }

                            if ($actionPlanAdvice instanceof UserActionPlanAdvice) {
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

                    $roofCatData = $buildingRoofTypeData[$roofCat] ?? [];
                    $extra = $roofCatData['extra'] ?? [];
                    //if (array_key_exists('zinc_replaced_date', $extra)) {
                    //    $zincReplaceYear = (int)$extra['zinc_replaced_date'];
                    //    // todo Get surface for $roofCat from building_roof_types (or elsewhere) for this input source
                    //    // Default: get from building_roof_types table for this input source
                    //    $roofType = RoofType::where('short', '=', $roofCat)->first();
                    //
                    //    $zincSurface = 0;
                    //    if ($roofType instanceof RoofType) {
                    //        $buildingRoofType = $this->building->roofTypes()->forInputSource($this->inputSource)->where('roof_type_id', '=', $roofType->id)->first();
                    //        if ($buildingRoofType instanceof BuildingRoofType) {
                    //            $zincSurface = $buildingRoofType->zinc_surface;
                    //        }
                    //    }
                    //    // Note there's no such request input just yet. We're not sure this will be available for the user
                    //    // to fill in.
                    //    $zincSurface = $roofCatData['zinc_surface'] ?? $zincSurface;
                    //
                    //    if ($zincReplaceYear > 0 && $zincSurface > 0) {
                    //        /** @var MeasureApplication $zincReplaceMeasure */
                    //        $zincReplaceMeasure = MeasureApplication::where('short', 'replace-zinc-' . $roofCat)->first();
                    //
                    //        $year = RoofInsulationCalculator::determineApplicationYear($zincReplaceMeasure, $zincReplaceYear, 1);
                    //        $costs = RawCalculator::calculateMeasureApplicationCosts($zincReplaceMeasure, $zincSurface, $year, false);
                    //
                    //        $actionPlanAdvice = new UserActionPlanAdvice(compact('year'));
                    //        $actionPlanAdvice->costs = UserActionPlanAdviceService::formatCosts($costs);
                    //        $actionPlanAdvice->input_source_id = $this->inputSource->id;
                    //        $actionPlanAdvice->user()->associate($this->user);
                    //        $actionPlanAdvice->userActionPlanAdvisable()->associate($zincReplaceMeasure);
                    //        $actionPlanAdvice->step()->associate($step);
                    //
                    //        // We only want to check old advices if the updated attributes are not relevant to this measure
                    //        if (! in_array($zincReplaceMeasure->id, $updatedMeasureIds) && $this->shouldCheckOldAdvices()) {
                    //            UserActionPlanAdviceService::checkOldAdvices($actionPlanAdvice, $zincReplaceMeasure,
                    //                $oldAdvices);
                    //        }
                    //
                    //        $actionPlanAdvice->save();
                    //    }
                    //}
                    if (array_key_exists('tiles_condition', $extra)) {
                        $tilesCondition = (int)$extra['tiles_condition'];

                        $surface = $roofCatData['roof_surface'] ?? 0;
                        if ($tilesCondition > 0 && $surface > 0) {
                            $replaceMeasure = MeasureApplication::where('short', 'replace-tiles')->first();
                            // no year here. Default is this year. It is incremented by factor * maintenance years
                            $year = Carbon::now()->year;
                            $roofTilesStatus = RoofTileStatus::find($tilesCondition);

                            if ($roofTilesStatus instanceof RoofTileStatus) {
                                $factor = ($roofTilesStatus->calculate_value / 100);

                                $year = RoofInsulationCalculator::determineApplicationYear($replaceMeasure, $year, $factor);
                                $costs = RawCalculator::calculateMeasureApplicationCosts($replaceMeasure, $surface, $year, false);

                                $actionPlanAdvice = new UserActionPlanAdvice(compact('year'));
                                $actionPlanAdvice->costs = UserActionPlanAdviceService::formatCosts($costs);
                                $actionPlanAdvice->input_source_id = $this->inputSource->id;
                                $actionPlanAdvice->user()->associate($this->user);
                                $actionPlanAdvice->userActionPlanAdvisable()->associate($replaceMeasure);
                                $actionPlanAdvice->step()->associate($step);

                                // We only want to check old advices if the updated attributes are not relevant to this measure
                                if (! in_array($replaceMeasure->id, $updatedMeasureIds) && $this->shouldCheckOldAdvices()) {
                                    UserActionPlanAdviceService::checkOldAdvices($actionPlanAdvice, $replaceMeasure,
                                        $oldAdvices);
                                }

                                $actionPlanAdvice->save();
                            }
                        }
                    }
                    if ($isBitumenRoof && array_key_exists('bitumen_replaced_date', $extra)) {
                        $bitumenReplaceYear = (int)$extra['bitumen_replaced_date'];
                        if ($bitumenReplaceYear <= 0) {
                            $bitumenReplaceYear = Carbon::now()->year - 10;
                        }

                        $surface = $roofCatData['roof_surface'] ?? 0;

                        if ($bitumenReplaceYear > 0 && $surface > 0) {
                            $replaceMeasure = MeasureApplication::where('short', 'replace-roof-insulation')->first();
                            // no percentages here. We just do this to keep the determineApplicationYear definition in one place
                            $year = $bitumenReplaceYear;
                            $factor = 1;

                            $year = RoofInsulationCalculator::determineApplicationYear($replaceMeasure, $year, $factor);
                            $costs = RawCalculator::calculateMeasureApplicationCosts($replaceMeasure, $surface, $year, false);

                            $actionPlanAdvice = new UserActionPlanAdvice(compact('year'));
                            $actionPlanAdvice->costs = UserActionPlanAdviceService::formatCosts($costs);
                            $actionPlanAdvice->input_source_id = $this->inputSource->id;
                            $actionPlanAdvice->user()->associate($this->user);
                            $actionPlanAdvice->userActionPlanAdvisable()->associate($replaceMeasure);
                            $actionPlanAdvice->step()->associate($step);

                            // We only want to check old advices if the updated attributes are not relevant to this measure
                            if (! in_array($replaceMeasure->id, $updatedMeasureIds) && $this->shouldCheckOldAdvices()) {
                                UserActionPlanAdviceService::checkOldAdvices($actionPlanAdvice, $replaceMeasure,
                                    $oldAdvices);
                            }

                            $actionPlanAdvice->save();
                        }
                    }
                }
            }
        }

        return $this;
    }

    public function saveValues(): ToolHelper
    {
        $buildingFeatureData = $this->getValues('building_features');
        // the selected roof types for the current situation
        // get the selected roof type ids
        $roofTypeIds = $this->getValues('building_roof_type_ids');
        $buildingRoofTypeData = $this->getValues('building_roof_types');

        // here we will store the data that we will need to create.
        // the building roof type data will always be filled, even though the roof type ids are not selected.
        $buildingRoofTypeCreateData = [];

        // so now we loop through the selected roof types so we can determine which $buildingRoofTypeData we will put in the $buildingRoofTypeCreateData.
        foreach ($roofTypeIds as $roofTypeId) {
            $roofType = RoofType::findOrFail($roofTypeId);
            if ($roofType instanceof RoofType) {
                //$buildingRoofType = $this->building->roofTypes()->forInputSource($this->inputSource)->where('roof_type_id', '=', $roofType->id)->first();
                //$zincSurface = $buildingRoofType instanceof BuildingRoofType ? $buildingRoofType->zinc_surface : 0;

                // Note there's no such request input just yet. We're not sure this will be available for the user
                // to fill in.
                //$buildingRoofTypeData[$roofType->short]['zinc_surface'] = $zincSurface;
                $buildingRoofTypeData[$roofType->short]['roof_type_id'] = $roofType->id;

                $buildingRoofTypeCreateData[] = $buildingRoofTypeData[$roofType->short];
            }
        }

        BuildingFeature::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
            [
                'building_id' => $this->building->id,
                'input_source_id' => $this->inputSource->id,
            ],
            $buildingFeatureData
        );

        // we don't know which roof_type_id we will get, so we delete all the rows and create new ones.
        ModelService::deleteAndCreate(
            BuildingRoofType::class,
            [
                'building_id' => $this->building->id,
                'input_source_id' => $this->inputSource->id,
            ],
            $buildingRoofTypeCreateData,
            true
        );

        return $this;
    }

    public function createValues(): ToolHelper
    {
        $buildingRoofTypes = $this->building->roofTypes()->forInputSource($this->masterInputSource)->get();

        // now lets handle the roof insulation stuff.
        $buildingRoofTypesArray = [];
        $buildingRoofTypeIds = [];

        /** @var BuildingRoofType $buildingRoofType */
        foreach ($buildingRoofTypes as $buildingRoofType) {
            $short = $buildingRoofType->roofType->short;
            $buildingRoofTypesArray[$short] = [
                'element_value_id' => $buildingRoofType->element_value_id,
                'roof_surface' => $buildingRoofType->roof_surface,
                'insulation_roof_surface' => $buildingRoofType->insulation_roof_surface,
                'extra' => $buildingRoofType->extra,
                'measure_application_id' => $buildingRoofType->extra['measure_application_id'] ?? null,
                'building_heating_id' => $buildingRoofType->building_heating_id,
            ];
            $buildingRoofTypeIds[] = $buildingRoofType->roofType->id;

            // if the roof is a flat roof OR the tiles_condition is empty: remove it!!
            // this is needed as the tiles condition has a different type of calculation
            // than bitumen has
            if (isset($buildingRoofTypesArray[$short]['extra']) && array_key_exists('tiles_condition', $buildingRoofTypesArray[$short]['extra'])) {
                if ('flat' == $short || empty($buildingRoofTypesArray[$short]['extra']['tiles_condition'])) {
                    unset($buildingRoofTypesArray[$short]['extra']['tiles_condition']);
                }
            }
        }

        $step = Step::findByShort('roof-insulation');
        $this->setValues([
            'considerables' => [
                $step->id => [
                    'is_considering' => $this->user->considers($step, $this->masterInputSource)
                ],
            ],
            'building_roof_types' => $buildingRoofTypesArray,
            'building_roof_type_ids' => $buildingRoofTypeIds,
            'building_features' => [
                'roof_type_id' => optional(
                    $this->building->buildingFeatures()->forInputSource($this->masterInputSource)->select('roof_type_id')->first()
                )->roof_type_id,
            ],
            'updated_measure_ids' => [],
        ]);

        return $this;
    }

    /**
     * Method to clear the building feature data for roof insulation step.
     */
    public static function clear(Building $building, InputSource $inputSource)
    {
        BuildingFeature::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
            [
                'building_id' => $building->id,
                'input_source_id' => $inputSource->id,
            ],
            [
                'roof_type_id' => null,
            ]
        );

        // delete my own building roof types.
        BuildingRoofType::forMe($building->user)->forInputSource($inputSource)->delete();

        StepCleared::dispatch($building->user, $inputSource, Step::findByShort('roof-insulation'));
    }
}
