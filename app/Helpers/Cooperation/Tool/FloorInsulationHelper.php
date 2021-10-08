<?php

namespace App\Helpers\Cooperation\Tool;

use App\Calculations\FloorInsulation;
use App\Events\StepCleared;
use App\Models\Building;
use App\Models\BuildingElement;
use App\Models\BuildingFeature;
use App\Models\Element;
use App\Models\ElementValue;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\Step;
use App\Models\UserActionPlanAdvice;
use App\Scopes\GetValueScope;
use App\Scopes\VisibleScope;
use App\Services\UserActionPlanAdviceService;

class FloorInsulationHelper extends ToolHelper
{
    public function createValues(): ToolHelper
    {
        $floorInsulationElement = Element::findByShort('floor-insulation');
        $crawlspaceElement = Element::findByShort('crawlspace');

        $buildingFeature = $this->building
            ->buildingFeatures()
            ->forInputSource($this->inputSource)
            ->first();

        $buildingElements = $this->building
            ->buildingElements()
            ->forInputSource($this->inputSource)
            ->get();

        $floorInsulationElementValueId = $buildingElements->where('element_id', $floorInsulationElement->id)->first()->element_value_id ?? null;
        $buildingCrawlspaceElement = $buildingElements->where('element_id', $crawlspaceElement->id)->first();

        $floorInsulationBuildingElements = [
            'element_value_id' => $buildingCrawlspaceElement->element_value_id ?? null,
            'extra' => [
                'has_crawlspace' => $buildingCrawlspaceElement->extra['has_crawlspace'] ?? null,
                'access' => $buildingCrawlspaceElement->extra['access'] ?? null,
            ],
        ];

        $floorBuildingFeatures = [
            'floor_surface' => $buildingFeature->floor_surface ?? null,
            'insulation_surface' => $buildingFeature->insulation_surface ?? null,
        ];

        $step = Step::findByShort('floor-insulation');
        $this->setValues([
            'considerables' => [
                $step->id => [
                    'is_considering' => $this->user->considers($step, $this->inputSource),
                ],
            ],
            'element' => [$floorInsulationElement->id => $floorInsulationElementValueId],
            'building_elements' => $floorInsulationBuildingElements,
            'building_features' => $floorBuildingFeatures,
        ]);

        return $this;
    }

    public function saveValues(): ToolHelper
    {
        $floorInsulationElement = Element::findByShort('floor-insulation');

        BuildingElement::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
            [
                'building_id' => $this->building->id,
                'input_source_id' => $this->inputSource->id,
                'element_id' => $floorInsulationElement->id,
            ],
            [
                'element_value_id' => $this->getValues('element')[$floorInsulationElement->id],
            ]
        );

        BuildingFeature::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
            [
                'building_id' => $this->building->id,
                'input_source_id' => $this->inputSource->id,
            ],
            $this->getValues('building_features')
        );

        $crawlspaceElement = Element::findByShort('crawlspace');
        BuildingElement::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
            [
                'building_id' => $this->building->id,
                'input_source_id' => $this->inputSource->id,
                'element_id' => $crawlspaceElement->id,
            ],
            $this->getValues('building_elements')
        );

        return $this;
    }

    public function createAdvices(array $updatedMeasureIds = []): ToolHelper
    {
        $floorInsulationElement = Element::findByShort('floor-insulation');
        $step = Step::findByShort('floor-insulation');

        $oldAdvices = UserActionPlanAdviceService::clearForStep($this->user, $this->inputSource, $step);

        $elementData = $this->getValues('element');

        if ($this->considers($step) && array_key_exists($floorInsulationElement->id, $elementData) && 'no' !== $this->getValues('building_elements.extra.has_crawlspace')) {
            $floorInsulationValue = ElementValue::where('element_id', $floorInsulationElement->id)
                ->where('id', $elementData[$floorInsulationElement->id])
                ->first();

            // don't save if not applicable
            if ($floorInsulationValue instanceof ElementValue && $floorInsulationValue->calculate_value < 5) {
                $userEnergyHabit = $this->user->energyHabit()->forInputSource($this->inputSource)->first();
                $results = FloorInsulation::calculate($this->building, $this->inputSource, $userEnergyHabit, $this->getValues());

                if (isset($results['insulation_advice']) && isset($results['cost_indication']) && $results['cost_indication'] > 0) {
                    $measureApplication = MeasureApplication::where('measure_name->nl', $results['insulation_advice'])
                        ->first(['measure_applications.*']);
                    if ($measureApplication instanceof MeasureApplication) {
                        $actionPlanAdvice = new UserActionPlanAdvice($results);
                        $actionPlanAdvice->input_source_id = $this->inputSource->id;
                        $actionPlanAdvice->costs = ['from' => $results['cost_indication']]; // only outlier
                        $actionPlanAdvice->user()->associate($this->user);
                        $actionPlanAdvice->userActionPlanAdvisable()->associate($measureApplication);
                        $actionPlanAdvice->step()->associate($step);

                        // We only want to check old advices if the updated attributes are not relevant to this measure
                        if (! in_array($measureApplication->id, $updatedMeasureIds)) {
                            UserActionPlanAdviceService::checkOldAdvices($actionPlanAdvice, $measureApplication,
                                $oldAdvices);
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
                'floor_surface' => null,
                'insulation_surface' => null,
            ]
        );

        StepCleared::dispatch($building->user, $inputSource, Step::findByShort('floor-insulation'));
    }
}
