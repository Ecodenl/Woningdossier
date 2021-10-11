<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Calculations\Ventilation;
use App\Helpers\Cooperation\Tool\VentilationHelper;
use App\Helpers\HoomdossierSession;
use App\Http\Requests\Cooperation\Tool\VentilationFormRequest;
use App\Models\BuildingService;
use App\Models\MeasureApplication;
use App\Models\ServiceValue;
use App\Models\Step;
use App\Services\ConsiderableService;
use App\Services\StepCommentService;
use Illuminate\Http\Request;

class VentilationController extends ToolController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $building = HoomdossierSession::getBuilding(true);

        /** @var BuildingService $buildingVentilationService */
        $buildingVentilationService = $building->getBuildingService('house-ventilation', HoomdossierSession::getInputSource(true));
        /** @var ServiceValue $buildingVentilation */
        $buildingVentilation = $buildingVentilationService->serviceValue;

        $howValues = VentilationHelper::getHowValues();
        $livingSituationValues = VentilationHelper::getLivingSituationValues();
        $usageValues = VentilationHelper::getUsageValues();

        return view('cooperation.tool.ventilation.index', compact(
            'building', 'buildingVentilation', 'howValues', 'livingSituationValues', 'usageValues'
        ));
    }

    /**
     * Method to store the data from the ventilation form.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(VentilationFormRequest $request)
    {
        $building = HoomdossierSession::getBuilding(true);
        $buildingOwner = $building->user;
        $inputSource = HoomdossierSession::getInputSource(true);

        $step = Step::findByShort('ventilation');

        // the actually checked considerables, so these are considered true
        $considerables = $request->input('considerables', []);

        // now get the measure applications the user did not check (so does not consider)
        $notConsiderableMeasureApplications = $step->measureApplications()->whereNotIn('id', array_keys($considerables))->get();
        // collect them al into one array, the VentilationHelper expects this format.
        foreach ($notConsiderableMeasureApplications as $measureApplication) {
            $considerables[$measureApplication->id] = ['is_considering' => false];
        }

        foreach ($considerables as $considerableId => $considerableData) {
            ConsiderableService::save(MeasureApplication::findOrFail($considerableId), $buildingOwner, $inputSource, $considerableData);
        }

        $stepComments = $request->input('step_comments');
        StepCommentService::save($building, $inputSource, $step, $stepComments);

        $dirtyAttributes = json_decode($request->input('dirty_attributes'), true);
        $updatedMeasureIds = [];

        // Currently, nothing on this page is relevant to the ventilation calculations. Therefore, there is
        // no benefit to recalculate from here
//        if (! empty($dirtyAttributes)) {
//            $updatedMeasureIds = MeasureApplication::findByShorts([
//                'ventilation-balanced-wtw', 'ventilation-decentral-wtw', 'ventilation-demand-driven', 'crack-sealing',
//            ])
//                ->pluck('id')
//                ->toArray();
//        }

        $values = $request->only('building_ventilations');
        $values['considerables'] = $considerables;
        $values['updated_measure_ids'] = $updatedMeasureIds;

        (new VentilationHelper($buildingOwner, $inputSource))
            ->setValues($values)
            ->saveValues()
            ->createAdvices();

        return $this->completeStore($this->step, $building, $inputSource);
    }

    public function calculate(Request $request)
    {
        $building = HoomdossierSession::getBuilding(true);
        $user = $building->user;
        $userEnergyHabit = $user->energyHabit;

        $result = Ventilation::calculate($building, HoomdossierSession::getInputSource(true), $userEnergyHabit, $request->all());

        return response()->json($result);
    }
}
