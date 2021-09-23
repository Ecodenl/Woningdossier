<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Calculations\Ventilation;
use App\Events\StepDataHasBeenChanged;
use App\Helpers\Cooperation\Tool\VentilationHelper;
use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Helpers\StepHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cooperation\Tool\VentilationFormRequest;
use App\Models\BuildingService;
use App\Models\MeasureApplication;
use App\Models\ServiceValue;
use App\Models\Step;
use App\Services\ConsiderableService;
use App\Services\StepCommentService;
use Illuminate\Http\Request;

class VentilationController extends Controller
{
    /**
     * @var Step
     */
    protected $step;

    public function __construct(Request $request)
    {
        $slug = str_replace('/tool/', '', $request->getRequestUri());
        $this->step = Step::where('slug', $slug)->first();
    }

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

        (new VentilationHelper($buildingOwner, $inputSource))
            ->setValues($request->only('building_ventilations', 'considerables'))
            ->saveValues()
            ->createAdvices();

        StepCommentService::save($building, $inputSource, $step, $request->input('step_comments.comment'));
        StepHelper::complete($step, $building, $inputSource);
        $building->update([
            'has_answered_expert_question' => true,
        ]);
        StepDataHasBeenChanged::dispatch($step, $building, Hoomdossier::user());
        $nextStep = StepHelper::getNextStep($building, $inputSource, $step);
        $url = $nextStep['url'];
        if (! empty($nextStep['tab_id'])) {
            $url .= '#'.$nextStep['tab_id'];
        }

        return redirect($url);
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
