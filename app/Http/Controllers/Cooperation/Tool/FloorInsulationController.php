<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Calculations\FloorInsulation;
use App\Helpers\Cooperation\Tool\FloorInsulationHelper;
use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Http\Requests\Cooperation\Tool\FloorInsulationFormRequest;
use App\Models\Building;
use App\Models\Element;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Services\ConsiderableService;
use App\Services\StepCommentService;

class FloorInsulationController extends ToolController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $typeIds = [4];
        /** @var Building $building */
        $building = HoomdossierSession::getBuilding(true);

        $buildingInsulation = $building->getBuildingElement('floor-insulation', $this->masterInputSource);
        $buildingInsulationForMe = $building->getBuildingElementsForMe('floor-insulation');

        $floorInsulation = optional($buildingInsulation)->element;

        $crawlspace = Element::where('short', 'crawlspace')->first();
        $buildingCrawlspace = $building->getBuildingElement($crawlspace->short, $this->masterInputSource);

        $crawlspacePresent = 2; // unknown
        if ($buildingCrawlspace instanceof \App\Models\BuildingElement) {
            if ($buildingCrawlspace->elementValue instanceof \App\Models\ElementValue) {
                $crawlspacePresent = 0; // yes
            }
        } else {
            $crawlspacePresent = 1; // now
        }

        $buildingElementsOrderedOnInputSourceCredibility = Hoomdossier::orderRelationShipOnInputSourceCredibility(
            $building->buildingElements()->where('element_id', $crawlspace->id)
        )->get();

        $buildingFeaturesOrderedOnInputSourceCredibility = Hoomdossier::orderRelationShipOnInputSourceCredibility(
            $building->buildingFeatures()
        )->get();

        return view('cooperation.tool.floor-insulation.index', compact(
            'floorInsulation', 'buildingInsulation', 'buildingInsulationForMe',
            'buildingElementsOrderedOnInputSourceCredibility',
            'crawlspace', 'buildingCrawlspace', 'typeIds', 'buildingFeaturesOrderedOnInputSourceCredibility',
            'crawlspacePresent', 'building'
        ));
    }

    public function calculate(FloorInsulationFormRequest $request)
    {
        /**
         * @var Building
         */
        $building = HoomdossierSession::getBuilding(true);
        $user = $building->user;

        $result = FloorInsulation::calculate(
            $building,
            $this->masterInputSource,
            $user->energyHabit()->forInputSource($this->masterInputSource)->first(),
            $request->all()
        );

        return response()->json($result);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(FloorInsulationFormRequest $request)
    {
        $building = HoomdossierSession::getBuilding(true);
        $user = $building->user;
        $inputSource = HoomdossierSession::getInputSource(true);

        ConsiderableService::save($this->step, $user, $inputSource,
            $request->validated()['considerables'][$this->step->id]);

        $stepComments = $request->input('step_comments');
        StepCommentService::save($building, $inputSource, $this->step, $stepComments['comment']);

        $dirtyAttributes = json_decode($request->input('dirty_attributes'), true);
        $updatedMeasureIds = [];
        // If anything's dirty, all measures must be recalculated
        if (! empty($dirtyAttributes)) {
            $updatedMeasureIds = MeasureApplication::findByShorts([
                'floor-insulation', 'bottom-insulation', 'floor-insulation-research',
            ])
                ->pluck('id')
                ->toArray();
        }

        $values = $request->validated();
        $values['updated_measure_ids'] = $updatedMeasureIds;

        (new FloorInsulationHelper($user, $inputSource))
            ->setValues($values)
            ->saveValues()
            ->createAdvices();

        return $this->completeStore($this->step, $building, $inputSource);
    }
}
