<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Calculations\HighEfficiencyBoiler;
use App\Events\StepDataHasBeenChanged;
use App\Helpers\Calculation\BankInterestCalculator;
use App\Helpers\Calculator;
use App\Helpers\HighEfficiencyBoilerCalculator;
use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Helpers\NumberFormatter;
use App\Helpers\StepHelper;
use App\Helpers\Translation;
use App\Http\Controllers\Controller;
use App\Http\Requests\HighEfficiencyBoilerFormRequest;
use App\Models\Building;
use App\Models\BuildingService;
use App\Models\Cooperation;
use App\Models\MeasureApplication;
use App\Models\Service;
use App\Models\ServiceValue;
use App\Models\Step;
use App\Models\UserActionPlanAdvice;
use App\Models\UserEnergyHabit;
use App\Models\UserInterest;
use App\Scopes\GetValueScope;
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

    public function calculate(Request $request)
    {
        $result = HighEfficiencyBoiler::calculate(Hoomdossier::user()->energyHabit, $request->all());

        return response()->json($result);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(HighEfficiencyBoilerFormRequest $request)
    {
        $building = HoomdossierSession::getBuilding(true);
        $user = $building->user;
        $buildingId = $building->id;
        $inputSourceId = HoomdossierSession::getInputSource();

        // Save the building service
        $buildingServices = $request->input('building_services', '');
        $buildingServiceId = key($buildingServices);

        $interests = $request->input('interest', '');
        UserInterest::saveUserInterests($user, $interests);

        $serviceValue = isset($buildingServices[$buildingServiceId]['service_value_id']) ? $buildingServices[$buildingServiceId]['service_value_id'] : '';
        $extra = isset($buildingServices[$buildingServiceId]['extra']) ? $buildingServices[$buildingServiceId]['extra'] : '';
        $comment = $request->input('comment', '');

        BuildingService::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
            [
                'building_id' => $buildingId,
                'service_id' => $buildingServiceId,
                'input_source_id' => $inputSourceId,
            ],
            [
                'service_value_id' => $serviceValue,
                'extra' => ['date' => $extra, 'comment' => $comment],
            ]
        );

        // save the habits
        $habits = $request->input('habit', '');
        $gasUsage = isset($habits['gas_usage']) ? $habits['gas_usage'] : '';
        $residentCount = isset($habits['resident_count']) ? $habits['resident_count'] : '';

        UserEnergyHabit::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
            [
                'user_id' => $user->id,
                'input_source_id' => $inputSourceId,
            ],
            [
                'resident_count' => $residentCount,
                'amount_gas' => $gasUsage,
            ]
        );

        // Save progress
        $this->saveAdvices($request);
        StepHelper::complete($this->step, $building, HoomdossierSession::getInputSource(true));
        StepDataHasBeenChanged::dispatch($this->step, $building, Hoomdossier::user());
        $cooperation = HoomdossierSession::getCooperation(true);

        $nextStep = StepHelper::getNextStep(Hoomdossier::user(), HoomdossierSession::getInputSource(true), $this->step);
        $url = route($nextStep['route'], ['cooperation' => $cooperation]);

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
        $results = $this->calculate($request);
        $results = $results->getData(true);

        // Remove old results
        UserActionPlanAdvice::forMe()->where('input_source_id', HoomdossierSession::getInputSource())->forStep($this->step)->delete();

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
