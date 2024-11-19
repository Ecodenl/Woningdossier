<?php

namespace App\Helpers\Cooperation\Tool;

use App\Calculations\WallInsulation;
use App\Events\StepCleared;
use App\Models\Building;
use App\Models\BuildingElement;
use App\Models\BuildingFeature;
use App\Models\Element;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\Step;
use App\Models\UserActionPlanAdvice;
use App\Scopes\GetValueScope;
use App\Scopes\VisibleScope;
use App\Services\UserActionPlanAdviceService;

class WallInsulationHelper extends ToolHelper
{

    public static function getFacadePlasteredPaintedValues(): array
    {
        return [
            1 => __('general.options.yes.title'),
            2 => __('general.options.no.title'),
            3 => __('general.options.unknown.title'),
        ];
    }
    public static function getCavityWallValues(): array
    {
        return [
            1 => __('general.options.yes.title'),
            2 => __('general.options.no.title'),
            0 => __('general.options.unknown.title'),
        ];
    }
    public function createValues(): ToolHelper
    {
        $buildingFeature = $this->building->buildingFeatures()->forInputSource($this->masterInputSource)->first();
        $wallInsulationElement = Element::findByShort('wall-insulation');
        $step = Step::findByShort('wall-insulation');

        $wallInsulationBuildingElement = $this->building
            ->buildingElements()
            ->where('element_id', $wallInsulationElement->id)
            ->forInputSource($this->masterInputSource)
            ->first();

        $this->setValues([
            'considerables' => [
                $step->id => [
                    'is_considering' => $this->user->considers($step, $this->masterInputSource),
                ],
            ],
            'element' => [$wallInsulationElement->id => $wallInsulationBuildingElement->element_value_id ?? null],
            'building_features' => [
                'cavity_wall' => $buildingFeature->cavity_wall ?? null,
                'wall_surface' => $buildingFeature->wall_surface ?? null,
                'insulation_wall_surface' => $buildingFeature->insulation_wall_surface ?? null,
                'wall_joints' => $buildingFeature->wall_joints ?? null,
                'contaminated_wall_joints' => $buildingFeature->contaminated_wall_joints ?? null,
                'facade_plastered_painted' => $buildingFeature->facade_plastered_painted ?? null,
                'facade_plastered_surface_id' => $buildingFeature->facade_plastered_surface_id ?? null,
                'facade_damaged_paintwork_id' => $buildingFeature->facade_damaged_paintwork_id ?? null,
            ],
            'updated_measure_ids' => [],
        ]);

        return $this;
    }

    public function saveValues(): ToolHelper
    {
        $wallInsulationElement = Element::findByShort('wall-insulation');

        // Save the wall insulation
        BuildingElement::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
            [
                'building_id' => $this->building->id,
                'input_source_id' => $this->inputSource->id,
                'element_id' => $wallInsulationElement->id,
            ],
            [
                'element_value_id' => $this->getValues('element')[$wallInsulationElement->id],
            ]
        );
        BuildingFeature::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
            [
                'building_id' => $this->building->id,
                'input_source_id' => $this->inputSource->id,
            ],
            $this->getValues('building_features')
        );

        return $this;
    }

    /**
     * Save the advices for the wall insulation page.
     *
     * @return \App\Helpers\Cooperation\Tool\ToolHelper
     */
    public function createAdvices(): ToolHelper
    {
        $updatedMeasureIds = $this->getValues('updated_measure_ids');

        $energyHabit = $this->user->energyHabit()->forInputSource($this->masterInputSource)->first();
        $results = WallInsulation::calculate($this->building, $this->masterInputSource, $energyHabit, $this->getValues());

        $step = Step::findByShort('wall-insulation');

        $oldAdvices = UserActionPlanAdviceService::clearForStep($this->user, $this->inputSource, $step);

        if ($this->considers($step)) {
            if (! is_null($results['measure']) && isset($results['cost_indication']) && $results['cost_indication'] > 0) {
                $measureApplication = MeasureApplication::findByShort($results['measure']);
                if ($measureApplication instanceof MeasureApplication) {
                    $actionPlanAdvice = new UserActionPlanAdvice($results);
                    $actionPlanAdvice->input_source_id = $this->inputSource->id;
                    $actionPlanAdvice->costs = UserActionPlanAdviceService::formatCosts($results['cost_indication']);
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

            $keysToMeasure = [
                'paint_wall' => 'paint-wall',
                'repair_joint' => 'repair-joint',
                'clean_brickwork' => 'clean-brickwork',
                'impregnate_wall' => 'impregnate-wall',
            ];

            foreach ($keysToMeasure as $key => $measureShort) {
                if (isset($results[$key]['costs']) && $results[$key]['costs'] > 0) {
                    $measureApplication = MeasureApplication::where('short', $measureShort)->first();
                    if ($measureApplication instanceof MeasureApplication) {
                        $actionPlanAdvice = new UserActionPlanAdvice($results[$key]);
                        $actionPlanAdvice->input_source_id = $this->inputSource->id;
                        $actionPlanAdvice->costs = UserActionPlanAdviceService::formatCosts($results[$key]['costs']);
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

    /**
     * Method to clear the building feature data for wall insulation step.
     */
    public static function clear(Building $building, InputSource $inputSource)
    {
        BuildingFeature::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
            [
                'building_id' => $building->id,
                'input_source_id' => $inputSource->id,
            ],
            [
                'facade_plastered_surface_id' => null,
                'wall_joints' => null,
                'cavity_wall' => null,
                'contaminated_wall_joints' => null,
                'wall_surface' => null,
                'insulation_wall_surface' => null,
                'facade_damaged_paintwork_id' => null,
                'facade_plastered_painted' => null,
            ]
        );

        StepCleared::dispatch($building->user, $inputSource, Step::findByShort('wall-insulation'));
    }
}
