<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Calculations\HighEfficiencyBoiler;
use App\Events\StepDataHasBeenChanged;
use App\Helpers\Cooperation\Tool\HighEfficiencyBoilerHelper;
use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Helpers\StepHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cooperation\Tool\HighEfficiencyBoilerFormRequest;
use App\Models\Service;
use App\Models\Step;
use App\Services\StepCommentService;
use Illuminate\Http\Request;

class HighEfficiencyBoilerController extends Controller
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
     * @return \Illuminate\Http\Response
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

        $stepComments = $request->input('step_comments');
        StepCommentService::save($building, $inputSource, $this->step, $stepComments['comment']);

        (new HighEfficiencyBoilerHelper($user, $inputSource))
            ->setValues($request->only('user_energy_habits', 'building_services'))
            ->saveValues()
            ->createAdvices();

        StepHelper::complete($this->step, $building, $inputSource);
        $building->update([
            'has_answered_expert_question' => true,
        ]);
        StepDataHasBeenChanged::dispatch($this->step, $building, Hoomdossier::user());

        $nextStep = StepHelper::getNextStep($building, $inputSource, $this->step);
        $url = $nextStep['url'];

        if (! empty($nextStep['tab_id'])) {
            $url .= '#'.$nextStep['tab_id'];
        }

        return redirect($url);
    }
}
