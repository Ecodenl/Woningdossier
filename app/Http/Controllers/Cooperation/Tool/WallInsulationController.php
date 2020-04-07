<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Calculations\WallInsulation;
use App\Events\StepDataHasBeenChanged;
use App\Helpers\Cooperation\Tool\WallInsulationHelper;
use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Helpers\StepHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cooperation\Tool\WallInsulationRequest;
use App\Models\Building;
use App\Models\BuildingElement;
use App\Models\BuildingFeature;
use App\Models\FacadeDamagedPaintwork;
use App\Models\FacadePlasteredSurface;
use App\Models\FacadeSurface;
use App\Models\InputSource;
use App\Models\Interest;
use App\Models\MeasureApplication;
use App\Models\Step;
use App\Models\UserActionPlanAdvice;
use App\Models\UserInterest;
use App\Scopes\GetValueScope;
use App\Services\StepCommentService;
use App\Services\UserInterestService;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WallInsulationController extends Controller
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
        $typeIds = [3];

        /** @var Building $building */
        $building = HoomdossierSession::getBuilding(true);

        $facadeInsulation = $building->getBuildingElement('wall-insulation');
        $buildingFeature = $building->buildingFeatures;
        $buildingElements = $facadeInsulation->element;

        $buildingFeaturesForMe = BuildingFeature::withoutGlobalScope(GetValueScope::class)->forMe()->get();

        /** @var BuildingElement $houseInsulation */
        $surfaces = FacadeSurface::orderBy('order')->get();
        $facadePlasteredSurfaces = FacadePlasteredSurface::orderBy('order')->get();
        $facadeDamages = FacadeDamagedPaintwork::orderBy('order')->get();

        $interests = Interest::orderBy('order')->get();

        return view('cooperation.tool.wall-insulation.index', compact(
             'building', 'facadeInsulation',
            'surfaces', 'buildingFeature', 'interests', 'typeIds',
            'facadePlasteredSurfaces', 'facadeDamages', 'buildingFeaturesForMe',
            'buildingElements'
        ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(WallInsulationRequest $request)
    {
        $building = HoomdossierSession::getBuilding(true);
        $inputSource = HoomdossierSession::getInputSource(true);
        $user = $building->user;
        $buildingId = $building->id;
        $inputSourceId = $inputSource->id;

        $userInterests = $request->input('user_interests');
        UserInterestService::save($user, $inputSource, $userInterests['interested_in_type'], $userInterests['interested_in_id'], $userInterests['interest_id']);

        $stepComments = $request->input('step_comments');
        StepCommentService::save($building, $inputSource, $this->step, $stepComments['comment']);


        $buildingFeatureData = $request->only([
            'facade_plastered_surface_id',
            'wall_joints',
            'cavity_wall',
            'contaminated_wall_joints',
            'wall_surface',
            'insulation_wall_surface',
            'facade_damaged_paintwork_id',
            'facade_plastered_painted'
        ]);

        // Get all the values from the form
        $wallInsulationQualities = $request->get('element', '');

        // Element id's and values
        $elementId = key($wallInsulationQualities);
        $elementValueId = reset($wallInsulationQualities);

        // Save the wall insulation
        BuildingElement::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
            [
                'building_id' => $buildingId,
                'input_source_id' => $inputSourceId,
                'element_id' => $elementId,
            ],
            [
                'element_value_id' => $elementValueId,
            ]
        );

        // when its a step, and a user has no interest in it we will clear the data for that step
        // a user may had interest in the step and later on decided he has no interest, so we clear the data to prevent weird data in the dumps.
        if (StepHelper::hasInterestInStep($user, Step::class, $this->step->id)) {
            WallInsulationHelper::save($building, $inputSource, $buildingFeatureData);
        } else {
            WallInsulationHelper::clear($building, $inputSource);
        }

        // Save progress
        $this->saveAdvices($request);
        StepHelper::complete($this->step, $building, HoomdossierSession::getInputSource(true));
        StepDataHasBeenChanged::dispatch($this->step, $building, Hoomdossier::user());

        $nextStep = StepHelper::getNextStep($building, HoomdossierSession::getInputSource(true), $this->step);
        $url = $nextStep['url'];

        if (! empty($nextStep['tab_id'])) {
            $url .= '#'.$nextStep['tab_id'];
        }

        return redirect($url);
    }

    protected function saveAdvices(Request $request)
    {
        $user = HoomdossierSession::getBuilding(true)->user;
        /** @var JsonResponse $results */
        $results = $this->calculate($request);
        $results = $results->getData(true);

        // Remove old results
        UserActionPlanAdvice::forMe()->where('input_source_id', HoomdossierSession::getInputSource())->forStep($this->step)->delete();

        if (isset($results['insulation_advice']) && isset($results['cost_indication']) && $results['cost_indication'] > 0) {
            $measureApplication = MeasureApplication::translated('measure_name', $results['insulation_advice'], 'nl')->first(['measure_applications.*']);
            if ($measureApplication instanceof MeasureApplication) {
                $actionPlanAdvice = new UserActionPlanAdvice($results);
                $actionPlanAdvice->costs = $results['cost_indication']; // only outlier
                $actionPlanAdvice->user()->associate($user);
                $actionPlanAdvice->measureApplication()->associate($measureApplication);
                $actionPlanAdvice->step()->associate($this->step);
                $actionPlanAdvice->save();
            }
        }

        $keysToMeasure = [
            'paint_wall' => 'paint-wall',
            'repair_joint' => 'repair-joint',
            'clean_brickwork' => 'clean-brickwork',
            'impregnate_wall' => 'impregnate-wall',
        ];

        foreach ($keysToMeasure as $key => $measureShort) {
            if (isset($results[$key]['costs']) && $results[$key]['costs'] > 0) {
                $measureApplication = MeasureApplication::where('short', $measureShort)->first();
                if ($measureApplication instanceof MeasureApplication) {
                    $actionPlanAdvice = new UserActionPlanAdvice($results[$key]);
                    $actionPlanAdvice->user()->associate($user);
                    $actionPlanAdvice->measureApplication()->associate($measureApplication);
                    $actionPlanAdvice->step()->associate($this->step);
                    $actionPlanAdvice->save();
                }
            }
        }
    }

    public function calculate(WallInsulationRequest $request)
    {
        $building = HoomdossierSession::getBuilding(true);
        $user = $building->user;
        $userEnergyHabit = $user->energyHabit;

        $result = WallInsulation::calculate($building, HoomdossierSession::getInputSource(true), $userEnergyHabit, $request->toArray());

        return response()->json($result);
    }
}
