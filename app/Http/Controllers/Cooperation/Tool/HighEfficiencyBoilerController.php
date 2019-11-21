<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Calculations\HighEfficiencyBoiler;
use App\Events\StepDataHasBeenChanged;
use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Helpers\StepHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cooperation\Tool\HighEfficiencyBoilerFormRequest;
use App\Models\BuildingService;
use App\Models\MeasureApplication;
use App\Models\Service;
use App\Models\Step;
use App\Models\User;
use App\Models\UserActionPlanAdvice;
use App\Models\UserEnergyHabit;
use App\Models\UserInterest;
use App\Scopes\GetValueScope;
use App\Services\StepCommentService;
use App\Services\UserInterestService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

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
        $habit = $buildingOwner->energyHabit;
        $energyHabitsForMe = UserEnergyHabit::forMe()->get();

        // NOTE: building element hr-boiler tells us if it's there
        $boiler = Service::where('short', 'boiler')->first();
        $boilerTypes = $boiler->values()->orderBy('order')->get();

        $installedBoiler = $building->buildingServices()->where('service_id', $boiler->id)->first();
        /** @var Collection $installedBoilerForMe */
        $installedBoilerForMe = $building->buildingServices()->forMe()->where('service_id', $boiler->id)->get();

        return view('cooperation.tool.hr-boiler.index', compact('building',
            'habit', 'boiler', 'boilerTypes', 'installedBoiler',
            'typeIds', 'installedBoilerForMe', 'energyHabitsForMe',
            'steps', 'buildingOwner'));
    }

    public function calculate(Request $request, User $buildingOwner)
    {
        $result = HighEfficiencyBoiler::calculate($buildingOwner->energyHabit, $request->all());

        return response()->json($result);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param HighEfficiencyBoilerFormRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(HighEfficiencyBoilerFormRequest $request)
    {
        $building = HoomdossierSession::getBuilding(true);
        $inputSource = HoomdossierSession::getInputSource(true);
        $user = $building->user;

        // Save the building service
        $userInterests = $request->input('user_interests');
        UserInterestService::save($user, $inputSource, $userInterests['interested_in_type'], $userInterests['interested_in_id'], $userInterests['interest_id']);

        $stepComments = $request->input('step_comments');
        StepCommentService::save($building, $inputSource, $this->step, $stepComments['comment']);

        $serviceValueId = $request->input('building_services.boiler.service_value_id');
        $date = $request->input('building_services.boiler.extra');


        $service = Service::findByShort('boiler');

        $building->buildingServices()->updateOrCreate(
            ['input_source_id' => $inputSource->id, 'service_id' => $service->id],
            ['service_value_id' => $serviceValueId, 'extra' => ['date' => $date]]
        );

        $user->energyHabit()->updateOrCreate(['input_source_id' => $inputSource->id], $request->input('user_energy_habits'));

        // Save progress
        $this->saveAdvices($request);
        StepHelper::complete($this->step, $building, $inputSource);
        StepDataHasBeenChanged::dispatch($this->step, $building, Hoomdossier::user());

        $nextStep = StepHelper::getNextStep($building, $inputSource, $this->step);
        $url = $nextStep['url'];

        if (! empty($nextStep['tab_id'])) {
            $url .= '#'.$nextStep['tab_id'];
        }

        return redirect($url);
    }

    protected function saveAdvices(Request $request)
    {
        $building = HoomdossierSession::getBuilding(true);
        $user = $building->user;

        /** @var JsonResponse $results */
        $results = $this->calculate($request, $user);
        $results = $results->getData(true);

        // Remove old results
        $user->actionPlanAdvices()->forStep($this->step)->delete();

        if (isset($results['cost_indication']) && $results['cost_indication'] > 0) {
            $measureApplication = MeasureApplication::where('short', 'high-efficiency-boiler-replace')->first();
            if ($measureApplication instanceof MeasureApplication) {
                $actionPlanAdvice = new UserActionPlanAdvice($results);
                $actionPlanAdvice->costs = $results['cost_indication'];
                $actionPlanAdvice->year = $results['replace_year'];
                $actionPlanAdvice->user()->associate($user);
                $actionPlanAdvice->measureApplication()->associate($measureApplication);
                $actionPlanAdvice->step()->associate($this->step);
                $actionPlanAdvice->save();
            }
        }
    }
}
