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
use App\Models\User;
use App\Models\UserActionPlanAdvice;
use App\Scopes\GetValueScope;
use App\Services\UserActionPlanAdviceService;
use Illuminate\Support\Facades\Input;
use phpDocumentor\Reflection\Types\Self_;

class FloorInsulationHelperv2
{
    /** @var User $user */
    public $user;

    /** @var InputSource $inputSource */
    public $inputSource;

    /** @var array */
    public $values;

    public function __construct(User $user, InputSource $inputSource)
    {

    }

    public function setValues(array $values)
    {
        $this->values = $values;
    }

    public static function buildRequestValues(Building $building, InputSource $inputSource)
    {
        $floorInsulationElement = Element::findByShort('floor-insulation');
        $crawlspaceElement = Element::findByShort('crawlspace');

        $buildingElements = $building->buildingElements()->forInputSource($inputSource)->get();

        // handle the stuff for the floor insulation.
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
        $floorInsulationElement = Element::findByShort('floor-insulation');

        BuildingElement::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
            [
                'building_id' => $building->id,
                'input_source_id' => $inputSource->id,
                'element_id' => $floorInsulationElement->id,
            ],
            [
                'element_value_id' => $saveData['element'][$floorInsulationElement->id]
            ]
        );

        BuildingFeature::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
            [
                'building_id' => $building->id,
                'input_source_id' => $inputSource->id,
            ],
            $saveData['building_features']
        );

        $crawlspaceElement = Element::findByShort('crawlspace');
        BuildingElement::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
            [
                'building_id' => $building->id,
                'input_source_id' => $inputSource->id,
                'element_id' => $crawlspaceElement->id,
            ],
            $saveData['building_elements']
        );

        self::saveAdvices($building, $inputSource, $saveData);
    }

    /**
     * @param Building $building
     * @param InputSource $inputSource
     * @param array $saveData
     * @throws \Exception
     */
    public static function saveAdvices(Building $building, InputSource $inputSource, array $saveData)
    {
        $user = $building->user;
        $floorInsulationElement = Element::findByShort('floor-insulation');
        $step = Step::findByShort('floor-insulation');

        UserActionPlanAdviceService::clearForStep($user, $inputSource, $step);

        $elementData = $saveData['element'];

        if (array_key_exists($floorInsulationElement->id, $elementData)) {

            $floorInsulationValue = ElementValue::where('element_id', $floorInsulationElement->id)
                ->where('id', $elementData[$floorInsulationElement->id])
                ->first();

            // don't save if not applicable
            if ($floorInsulationValue instanceof ElementValue && $floorInsulationValue->calculate_value < 5) {

                $results = FloorInsulation::calculate($building, $inputSource, $user->energyHabit, $saveData);

                if (isset($results['insulation_advice']) && isset($results['cost_indication']) && $results['cost_indication'] > 0) {

                    $measureApplication = MeasureApplication::translated('measure_name', $results['insulation_advice'], 'nl')
                        ->first(['measure_applications.*']);
                    if ($measureApplication instanceof MeasureApplication) {
                        $actionPlanAdvice = new UserActionPlanAdvice($results);
                        $actionPlanAdvice->costs = $results['cost_indication']; // only outlier
                        $actionPlanAdvice->user()->associate($user);
                        $actionPlanAdvice->measureApplication()->associate($measureApplication);
                        $actionPlanAdvice->step()->associate($step);
                        $actionPlanAdvice->save();
                    }
                }
            }
        }
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
                'floor_surface' => null,
                'insulation_surface' => null
            ]
        );

        StepCleared::dispatch($building->user, $inputSource, Step::findByShort('floor-insulation'));
    }
}