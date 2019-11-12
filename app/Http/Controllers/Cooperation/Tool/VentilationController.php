<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Calculations\Ventilation;
use App\Events\StepDataHasBeenChanged;
use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Helpers\StepHelper;
use App\Http\Controllers\Controller;
use App\Models\BuildingService;
use App\Models\Interest;
use App\Models\MeasureApplication;
use App\Models\ServiceValue;
use App\Models\Step;
use App\Services\UserInterestService;
use Illuminate\Http\Request;

class VentilationController extends Controller
{
    /**
     * @var Step
     */
    protected $step;

    public function __construct(Request $request)
    {
        $slug       = str_replace('/tool/', '', $request->getRequestUri());
        $this->step = Step::where('slug', $slug)->first();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $building = HoomdossierSession::getBuilding(true);

        /** @var BuildingService $buildingVentilationService */
        $buildingVentilationService = $building->getBuildingService('house-ventilation',
            HoomdossierSession::getInputSource(true));
        /** @var ServiceValue $buildingVentilation */
        $buildingVentilation = $buildingVentilationService->serviceValue;

        return view('cooperation.tool.ventilation.index',
            compact('building', 'buildingVentilation'));
        //return view('cooperation.tool.ventilation-information.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $building = HoomdossierSession::getBuilding(true);
        $buildingOwner = $building->user;
        $inputSource = HoomdossierSession::getInputSource(true);
        //$step = Step::findByShort('ventilation');
        // replace me with above
        $step = Step::where('slug', '=', 'ventilation')->first();

        $interestsInMeasureApplications = $request->input('user_interests', []);
        $yesOnShortNotice = Interest::orderBy('calculate_value')->first();

        foreach ($interestsInMeasureApplications as $measureApplicationId) {
            //UserInterestService::save($buildingOwner, $inputSource, MeasureApplication::class, $measureApplicationId , $yesOnShortNotice->id);
        }

        // Save ventilation data
        $building->buildingVentilations()->updateOrCreate(['input_source_id' => $inputSource->id, ], $request->input('building_ventilations'));

        //StepCommentService::save($building, $inputSource, $step, $request->input('step_comments.comment'));
        StepHelper::complete($step, $building, $inputSource);
        StepDataHasBeenChanged::dispatch($step, $building, Hoomdossier::user());
        $nextStep = StepHelper::getNextStep($building, $inputSource, $step);
        $url = $nextStep['url'];
        if (!empty($nextStep['tab_id'])) {
            $url .= '#' . $nextStep['tab_id'];
        }
        return redirect($url);
    }

    public function calculate(Request $request)
    {
        $building = HoomdossierSession::getBuilding(true);
        $user = $building->user;
        $userEnergyHabit = $user->energyHabit;

        $result = Ventilation::calculate($building, HoomdossierSession::getInputSource(true), $userEnergyHabit, $request->toArray());

        return response()->json($result);
    }
}
