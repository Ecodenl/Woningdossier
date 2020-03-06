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
use App\Models\Interest;
use App\Models\MeasureApplication;
use App\Models\ServiceValue;
use App\Models\Step;
use App\Models\UserActionPlanAdvice;
use App\Models\UserInterest;
use App\Services\UserInterestService;
use App\Services\StepCommentService;
use Illuminate\Http\JsonResponse;
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
     * @param VentilationFormRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(VentilationFormRequest $request)
    {
        $building = HoomdossierSession::getBuilding(true);
        $buildingOwner = $building->user;
        $inputSource = HoomdossierSession::getInputSource(true);

        $step = Step::findByShort('ventilation');

        $interestsInMeasureApplications = $request->input('user_interests', []);
        $noInterestInMeasureApplications = $step->measureApplications()->whereNotIn('id', $interestsInMeasureApplications)->get();

        // this will be the interest when the checkbox is not checked
        $noInterest = Interest::where('calculate_value', 4)->first();

        // default interest for measure application when checked: interest in the step itself
        $defaultInterest = Interest::orderBy('calculate_value')->first();
        $stepUserInterest = $building->user->userInterestsForSpecificType(get_class($step), $step->id, $inputSource)->first();
        if ($stepUserInterest instanceof UserInterest) {
            $defaultInterest = $stepUserInterest->interest;
        }


        foreach ($interestsInMeasureApplications as $measureApplicationId) {
            UserInterestService::save($buildingOwner, $inputSource, MeasureApplication::class, $measureApplicationId, $defaultInterest->id);
        }
        foreach ($noInterestInMeasureApplications as $measureApplicationWithNoInterest) {
            UserInterestService::save($buildingOwner, $inputSource, MeasureApplication::class, $measureApplicationWithNoInterest->id, $noInterest->id);
        }

        $houseVentilationData = $request->input('building_ventilations');

        // Save ventilation data
        $building->buildingVentilations()
            ->updateOrCreate(
                [
                    'input_source_id' => $inputSource->id,
                ],
                [
                    'how' => $houseVentilationData['how'] ?? [],
                    'usage' => $houseVentilationData['usage'] ?? [],
                    'living_situation' => $houseVentilationData['living_situation'] ?? [],
                ]
            );

        $this->saveAdvices($request);
        StepCommentService::save($building, $inputSource, $step, $request->input('step_comments.comment'));
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

        $result = Ventilation::calculate($building, HoomdossierSession::getInputSource(true), $userEnergyHabit, $request->all());

        return response()->json($result);
    }

    protected function saveAdvices(Request $request)
    {
        $buildingOwner = HoomdossierSession::getBuilding(true)->user;
        /** @var JsonResponse $results */
        $results = $this->calculate($request);
        $results = $results->getData(true);

        // Remove old results
        UserActionPlanAdvice::forMe()->where('input_source_id', HoomdossierSession::getInputSource())->forStep($this->step)->delete();

        $interestsInMeasureApplications = $request->input('user_interests', []);
        $relevantAdvices = collect($results['advices'])->whereIn('id', $interestsInMeasureApplications);

        foreach ($relevantAdvices as $advice) {
            $measureApplication = MeasureApplication::find($advice['id']);
            if ($measureApplication instanceof MeasureApplication) {
                if ($measureApplication->short == 'crack-sealing') {
                    $actionPlanAdvice = new UserActionPlanAdvice($results['result']['crack_sealing'] ?? []);
                    $actionPlanAdvice->costs = $results['result']['crack_sealing']['cost_indication'] ?? null; // only outlier
                } else {
                    $actionPlanAdvice = new UserActionPlanAdvice();
                }

                $actionPlanAdvice->planned = true;
                $actionPlanAdvice->user()->associate($buildingOwner);
                $actionPlanAdvice->measureApplication()->associate($measureApplication);
                $actionPlanAdvice->step()->associate($this->step);
                $actionPlanAdvice->save();
            }
        }
    }
}
