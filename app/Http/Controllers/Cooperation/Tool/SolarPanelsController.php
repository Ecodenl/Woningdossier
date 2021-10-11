<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Calculations\SolarPanel;
use App\Helpers\Cooperation\Tool\SolarPanelHelper;
use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Http\Requests\Cooperation\Tool\SolarPanelFormRequest;
use App\Models\MeasureApplication;
use App\Models\PvPanelOrientation;
use App\Services\ConsiderableService;
use App\Services\StepCommentService;
use Illuminate\Http\Request;

class SolarPanelsController extends ToolController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $typeIds = [7];

        $building = HoomdossierSession::getBuilding(true);
        $buildingOwner = $building->user;

        $pvPanelOrientations = PvPanelOrientation::orderBy('order')->get();

        $energyHabitsOrderedOnInputSourceCredibility = Hoomdossier::orderRelationShipOnInputSourceCredibility(
            $buildingOwner->energyHabit()
        )->get();

        $pvPanelsOrderedOnInputSourceCredibility = Hoomdossier::orderRelationShipOnInputSourceCredibility(
            $building->pvPanels()
        )->get();

        return view('cooperation.tool.solar-panels.index',
            compact(
                'building', 'pvPanelOrientations', 'buildingOwner', 'typeIds',
                'energyHabitsOrderedOnInputSourceCredibility', 'pvPanelsOrderedOnInputSourceCredibility'
            )
        );
    }

    public function calculate(Request $request)
    {
        $building = HoomdossierSession::getBuilding(true);
        $result = SolarPanel::calculate($building, $request->all());

        return response()->json($result);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Cooperation\Tool\SolarPanelFormRequest  $request
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(SolarPanelFormRequest $request)
    {
        $building = HoomdossierSession::getBuilding(true);
        $inputSource = HoomdossierSession::getInputSource(true);
        $user = $building->user;

        ConsiderableService::save($this->step, $user, $inputSource, $request->validated()['considerables'][$this->step->id]);

        $stepComments = $request->input('step_comments');
        StepCommentService::save($building, $inputSource, $this->step, $stepComments['comment']);

        $dirtyAttributes = json_decode($request->input('dirty_attributes'), true);
        $updatedMeasureIds = [];
        // If anything's dirty, all measures must be recalculated (we can't really check specifics here)
        if (! empty($dirtyAttributes)) {
            $updatedMeasureIds = MeasureApplication::findByShorts([
                'solar-panels-place-replace',
            ])
                ->pluck('id')
                ->toArray();
        }

        $values = $request->only('building_pv_panels', 'user_energy_habits', 'considerables');
        $values['updated_measure_ids'] = $updatedMeasureIds;

        (new SolarPanelHelper($user, $inputSource))
            ->setValues($values)
            ->saveValues()
            ->createAdvices();

        return $this->completeStore($this->step, $building, $inputSource);
    }
}
