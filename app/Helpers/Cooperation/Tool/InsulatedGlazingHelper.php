<?php

namespace App\Helpers\Cooperation\Tool;

use App\Calculations\InsulatedGlazing;
use App\Models\BuildingElement;
use App\Models\BuildingFeature;
use App\Models\BuildingInsulatedGlazing;
use App\Models\BuildingPaintworkStatus;
use App\Models\Element;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\Step;
use App\Models\UserActionPlanAdvice;
use App\Scopes\GetValueScope;
use App\Services\ModelService;
use App\Services\UserActionPlanAdviceService;
use Illuminate\Support\Collection;

class InsulatedGlazingHelper extends ToolHelper
{
    public function saveValues(): ToolHelper
    {
        $buildingFeatureData = $this->getValues('building_features');
        $buildingInsulatedGlazingData = $this->getValues('building_insulated_glazings');
        $buildingElementData = $this->getValues('building_elements');
        $buildingPaintworkStatusData = $this->getValues('building_paintwork_statuses');

        foreach ($buildingInsulatedGlazingData as $measureApplicationId => $buildingInsulatedGlazing) {
            // update or Create the buildingInsulatedGlazing
            BuildingInsulatedGlazing::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
                [
                    'building_id' => $this->building->id,
                    'input_source_id' => $this->inputSource->id,
                    'measure_application_id' => $measureApplicationId,
                ],
                $buildingInsulatedGlazing
            );
        }

        $frames = Element::where('short', 'frames')->first();
        $woodElements = Element::where('short', 'wood-elements')->first();

        // lets save the frame element value (main element)
        BuildingElement::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
            [
                'building_id' => $this->building->id,
                'input_source_id' => $this->inputSource->id,
                'element_id' => $frames->id,
            ],
            [
                'element_value_id' => $buildingElementData[$frames->id],
            ]
        );

        // collect the wood element create data
        // after that we can delete the old records and create the new ones
        $woodElementCreateData = [];
        if (array_key_exists($woodElements->id, $buildingElementData)) {
            foreach ($buildingElementData[$woodElements->id] as $woodElementValueId) {
                $woodElementCreateData[] = [
                    'element_value_id' => $woodElementValueId,
                ];
            }
        }
        ModelService::deleteAndCreate(BuildingElement::class,
            [
                'building_id' => $this->building->id,
                'input_source_id' => $this->inputSource->id,
                'element_id' => $woodElements->id,
            ],
            $woodElementCreateData
        );

        BuildingFeature::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
            [
                'building_id' => $this->building->id,
                'input_source_id' => $this->inputSource->id,
            ],
            $buildingFeatureData
        );

        $lastPaintedYear = null;
        if (array_key_exists('last_painted_year', $buildingPaintworkStatusData)) {
            $year = (int) $buildingPaintworkStatusData['last_painted_year'];
            if ($year > 1950) {
                $buildingPaintworkStatusData['last_painted_year'] = $year;
            }
        }
        BuildingPaintworkStatus::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
            [
                'building_id' => $this->building->id,
                'input_source_id' => $this->inputSource->id,
            ],
            $buildingPaintworkStatusData
        );

        return $this;
    }

    public function createAdvices(): ToolHelper
    {
        $updatedMeasureIds = $this->getValues('updated_measure_ids');

        $step = Step::findByShort('insulated-glazing');

        $energyHabit = $this->user->energyHabit()->forInputSource($this->masterInputSource)->first();
        $results = InsulatedGlazing::calculate($this->building, $this->masterInputSource, $energyHabit, $this->getValues());

        $oldAdvices = UserActionPlanAdviceService::clearForStep($this->user, $this->inputSource, $step);

        foreach ($results['measure'] as $measureId => $data) {
            $measureApplication = MeasureApplication::where('id', $measureId)->where('step_id', $step->id)->first();

            if ($this->considers($measureApplication) && array_key_exists('costs', $data) && $data['costs'] > 0) {

                if ($measureApplication instanceof MeasureApplication) {
                    $actionPlanAdvice = new UserActionPlanAdvice($data);
                    $actionPlanAdvice->costs = UserActionPlanAdviceService::formatCosts($data['costs']);
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

        $keysToMeasure = [
            'paintwork' => 'paint-wood-elements',
            // moved to ventilation, keeping this commented for future reference
            // 'crack-sealing' => 'crack-sealing',
        ];

        foreach ($keysToMeasure as $key => $measureShort) {
            if (isset($results[$key]['costs']) && $results[$key]['costs'] > 0) {
                $measureApplication = MeasureApplication::where('short', $measureShort)->first();
                if ($measureApplication instanceof MeasureApplication) {
                    $actionPlanAdvice = new UserActionPlanAdvice($results[$key]);
                    $actionPlanAdvice->costs = UserActionPlanAdviceService::formatCosts($results[$key]['costs']);
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

        return $this;
    }

    public function createValues(): ToolHelper
    {
        $buildingFeature = $this
            ->building
            ->buildingFeatures()
            ->forInputSource($this->masterInputSource)
            ->first();

        $buildingPaintworkStatus = $this
            ->building
            ->currentPaintworkStatus()
            ->forInputSource($this->masterInputSource)
            ->first();

        $buildingPaintworkStatusesArray = [
            'last_painted_year' => $buildingPaintworkStatus->last_painted_year ?? null,
            'paintwork_status_id' => $buildingPaintworkStatus->paintwork_status_id ?? null,
            'wood_rot_status_id' => $buildingPaintworkStatus->wood_rot_status_id ?? null,
        ];

        /** @var Collection $buildingInsulatedGlazings */
        $buildingInsulatedGlazings = $this->building
            ->currentInsulatedGlazing()
            ->forInputSource($this->masterInputSource)
            ->select('measure_application_id', 'insulating_glazing_id', 'building_heating_id', 'm2', 'windows')
            ->get();

        // build the right structure for the calculation
        $buildingInsulatedGlazingArray = [];
        foreach ($buildingInsulatedGlazings as $buildingInsulatedGlazing) {
            $buildingInsulatedGlazingArray[$buildingInsulatedGlazing->measure_application_id] = [
                'insulating_glazing_id' => $buildingInsulatedGlazing->insulating_glazing_id,
                'building_heating_id' => $buildingInsulatedGlazing->building_heating_id,
                'm2' => $buildingInsulatedGlazing->m2,
                'windows' => $buildingInsulatedGlazing->windows,
            ];
        }

        $woodElements = Element::findByShort('wood-elements');
        $frames = Element::findByShort('frames');
        $buildingElements = $this->building->buildingElements()->forInputSource($this->masterInputSource)->get();

        // handle the wood / frame / crack sealing elements for the insulated glazing
        $buildingElementsArray = [];

        $buildingWoodElement = $buildingElements->where('element_id', $woodElements->id)->pluck('element_value_id')->toArray();
        $buildingElementsArray[$woodElements->id] = array_combine($buildingWoodElement, $buildingWoodElement) ?? null;

        $buildingFrameElement = $buildingElements->where('element_id', $frames->id)->first();
        $buildingElementsArray[$frames->id] = $buildingFrameElement->element_value_id ?? null;


        $measureApplicationIds = MeasureApplication::whereIn('short', [
            'hrpp-glass-only',
            'hrpp-glass-frames',
            'hr3p-frames',
            'glass-in-lead',
        ])->select('id')->pluck('id');

        $considerablesForMeasures =
            $this->user
                ->considerables(MeasureApplication::class)
                ->wherePivot('input_source_id', $this->masterInputSource->id)
                ->wherePivotIn('considerable_id', $measureApplicationIds)
                ->get()->keyBy('pivot.considerable_id')
                ->map(function($considerable) {
                    return [
                        'is_considering' => $considerable->pivot->is_considering
                    ];
                })->toArray();

        $this->setValues([
            'considerables' => $considerablesForMeasures,
            'building_insulated_glazings' => $buildingInsulatedGlazingArray,
            'building_elements' => $buildingElementsArray,
            'building_features' => ['window_surface' => $buildingFeature->window_surface ?? null],
            'building_paintwork_statuses' => $buildingPaintworkStatusesArray,
            'updated_measure_ids' => [],
        ]);

        return $this;
    }

    // not used at the moment as there is no main question for the element.
//    /**
//     * Method to clear the building feature data for wall insulation step.
//     *
//     * @param Building $building
//     * @param InputSource $inputSource
//     */
//    public static function clear(Building $building, InputSource $inputSource)
//    {
//        $frames = Element::where('short', 'frames')->first();
//        $woodElements = Element::where('short', 'wood-elements')->first();
//
//        BuildingFeature::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
//            [
//                'building_id' => $building->id,
//                'input_source_id' => $inputSource->id,
//            ],
//            [
//                'window_surface' => null
//            ]
//        );
//
//        // delete the building elements for the page, wood element and frame
//        BuildingElement::forMe($building->user)
//            ->forInputSource($inputSource)
//            ->where(function ($query) use ($woodElements, $frames) {
//                return $query->where('element_id', $woodElements->id)
//                    ->orWhere('element_id', $frames->id);
//            })->delete();
//
//        BuildingPaintworkStatus::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
//            [
//                'building_id' => $building->id,
//                'input_source_id' => $inputSource->id,
//            ],
//            [
//                'last_painted_year' => null,
//                'paintwork_status_id' => null,
//                'wood_rot_status_id' => null
//            ]
//        );
//
//        StepCleared::dispatch($building->user, $inputSource, Step::findByShort('insulated-glazing'));
//    }
}
