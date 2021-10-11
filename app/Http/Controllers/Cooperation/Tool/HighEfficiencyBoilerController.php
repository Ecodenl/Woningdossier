<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Calculations\HighEfficiencyBoiler;
use App\Helpers\Cooperation\Tool\HighEfficiencyBoilerHelper;
use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Http\Requests\Cooperation\Tool\HighEfficiencyBoilerFormRequest;
use App\Models\MeasureApplication;
use App\Models\Service;
use App\Services\ConsiderableService;
use App\Services\StepCommentService;
use Illuminate\Http\Request;

class HighEfficiencyBoilerController extends ToolController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $typeIds = [4];

        $building = HoomdossierSession::getBuilding(true);
        $buildingOwner = $building->user;

        // NOTE: building element hr-boiler tells us if it's there
        $boiler = Service::where('short', 'boiler')->first();
        $boilerTypes = $boiler->values()->orderBy('order')->get();

        $installedBoiler = $building->buildingServices()->where('service_id', $boiler->id)->first();

        $userEnergyHabitsOrderedOnInputSourceCredibility = Hoomdossier::orderRelationShipOnInputSourceCredibility(
            $buildingOwner->energyHabit()
        )->get();

        $buildingServicesOrderedOnInputSourceCredibility = Hoomdossier::orderRelationShipOnInputSourceCredibility(
            $building->buildingServices()->where('service_id', $boiler->id)
        )->get();

        return view('cooperation.tool.hr-boiler.index', compact('building',
            'boiler', 'boilerTypes', 'installedBoiler',
            'typeIds', 'userEnergyHabitsOrderedOnInputSourceCredibility',
             'buildingOwner', 'buildingServicesOrderedOnInputSourceCredibility'
        ));
    }

    public function calculate(Request $request)
    {
        $result = HighEfficiencyBoiler::calculate(HoomdossierSession::getBuilding(true)->user->energyHabit, $request->all());

        return response()->json($result);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(HighEfficiencyBoilerFormRequest $request)
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
                'high-efficiency-boiler-replace',
            ])
                ->pluck('id')
                ->toArray();
        }

        $values = $request->only('user_energy_habits', 'building_services', 'considerables');
        $values['updated_measure_ids'] = $updatedMeasureIds;

        (new HighEfficiencyBoilerHelper($user, $inputSource))
            ->setValues($values)
            ->saveValues()
            ->createAdvices();

        return $this->completeStore($this->step, $building, $inputSource);
    }
}
