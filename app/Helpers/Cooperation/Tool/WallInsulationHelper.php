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
    public function createValues(): ToolHelper
    {
        $buildingFeature = $this->building->buildingFeatures()->forInputSource($this->inputSource)->first();
        $wallInsulationElement = Element::findByShort('wall-insulation');

        $wallInsulationBuildingElement = $this->building
            ->buildingElements()
            ->where('element_id', $wallInsulationElement->id)
            ->forInputSource($this->inputSource)
            ->first();

        $this->setValues([
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
        $energyHabit = $this->user->energyHabit()->forInputSource($this->inputSource)->first();
        $results = WallInsulation::calculate($this->building, $this->inputSource, $energyHabit, $this->getValues());

        $step = Step::findByShort('wall-insulation');

        $masterInputSource = InputSource::findByShort(InputSource::MASTER_SHORT)     ;

        $oldAdvices = UserActionPlanAdviceService::clearForStep($this->user, $this->inputSource, $step);

        if (isset($results['insulation_advice']) && isset($results['cost_indication']) && $results['cost_indication'] > 0) {
            $measureApplication = MeasureApplication::where('measure_name->nl', $results['insulation_advice'])->first();
            if ($measureApplication instanceof MeasureApplication) {
                $actionPlanAdvice = new UserActionPlanAdvice($results);
                $actionPlanAdvice->input_source_id = $this->inputSource->id;
                $actionPlanAdvice->costs = ['from' => $results['cost_indication']]; // only outlier
                $actionPlanAdvice->user()->associate($this->user);
                $actionPlanAdvice->userActionPlanAdvisable()->associate($measureApplication);
                $actionPlanAdvice->step()->associate($step);

                UserActionPlanAdviceService::checkOldAdvices($actionPlanAdvice, $measureApplication, $oldAdvices);

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
                    $actionPlanAdvice->costs = ['from' => $results[$key]['costs']]; // only outlier
                    $actionPlanAdvice->user()->associate($this->user);
                    $actionPlanAdvice->userActionPlanAdvisable()->associate($measureApplication);
                    $actionPlanAdvice->step()->associate($step);

                    UserActionPlanAdviceService::checkOldAdvices($actionPlanAdvice, $measureApplication, $oldAdvices);

                    $actionPlanAdvice->save();
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
