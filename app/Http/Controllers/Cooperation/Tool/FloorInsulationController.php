<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Calculations\FloorInsulation;
use App\Helpers\Cooperation\Tool\FloorInsulationHelper;
use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Http\Requests\Cooperation\Tool\FloorInsulationFormRequest;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\Element;
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

        $buildingInsulation = $building->getBuildingElement('floor-insulation');
        $buildingInsulationForMe = $building->getBuildingElementsForMe('floor-insulation');

        $floorInsulation = optional($buildingInsulation)->element;

        $crawlspace = Element::where('short', 'crawlspace')->first();
        $buildingCrawlspace = $building->getBuildingElement($crawlspace->short);

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
            'floorInsulation', 'buildingInsulation', 'buildingInsulationForMe', 'buildingElementsOrderedOnInputSourceCredibility',
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

        $result = FloorInsulation::calculate($building, HoomdossierSession::getInputSource(true), $user->energyHabit, $request->all());

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

        ConsiderableService::save($this->step, $user, $inputSource, $request->validated()['considerables'][$this->step->id]);

        $stepComments = $request->input('step_comments');
        StepCommentService::save($building, $inputSource, $this->step, $stepComments['comment']);

        (new FloorInsulationHelper($user, $inputSource))
            ->setValues($request->validated())
            ->saveValues()
            ->createAdvices();

        return $this->completeStore($this->step, $building, $inputSource);
    }
}
