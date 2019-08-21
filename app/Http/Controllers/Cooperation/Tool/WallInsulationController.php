<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Calculations\WallInsulation;
use App\Events\StepDataHasBeenChanged;
use App\Helpers\Calculation\BankInterestCalculator;
use App\Helpers\Calculator;
use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Helpers\KeyFigures\WallInsulation\Temperature;
use App\Helpers\NumberFormatter;
use App\Helpers\StepHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\WallInsulationRequest;
use App\Models\Building;
use App\Models\BuildingElement;
use App\Models\BuildingFeature;
use App\Models\Cooperation;
use App\Models\ElementValue;
use App\Models\FacadeDamagedPaintwork;
use App\Models\FacadePlasteredSurface;
use App\Models\FacadeSurface;
use App\Models\Interest;
use App\Models\MeasureApplication;
use App\Models\Step;
use App\Models\UserActionPlanAdvice;
use App\Models\UserEnergyHabit;
use App\Models\UserInterest;
use App\Scopes\GetValueScope;
use Carbon\Carbon;
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
        $building = Building::find(HoomdossierSession::getBuilding());

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
        $building = Building::find(HoomdossierSession::getBuilding());
        $user = $building->user;
        $buildingId = $building->id;
        $inputSourceId = HoomdossierSession::getInputSource();

        $interests = $request->input('interest', []);
        UserInterest::saveUserInterests($user, $interests);

        // Get all the values from the form
        $wallInsulationQualities = $request->get('element', '');
        $plasteredWallSurface = $request->get('facade_plastered_surface_id', '');
        $damagedPaintwork = $request->get('facade_damaged_paintwork_id', 0);
        $wallJoints = $request->get('wall_joints', '');
        $wallJointsContaminated = $request->get('contaminated_wall_joints', '');
        $wallSurface = $request->get('wall_surface', 0);
        $insulationWallSurface = $request->get('insulation_wall_surface', 0);
        $additionalInfo = $request->get('additional_info', '');
        $cavityWall = $request->get('cavity_wall', '');
        $facadePlasteredOrPainted = $request->get('facade_plastered_painted', '');

        // Element id's and values
        $elementId = key($wallInsulationQualities);
        $elementValueId = reset($wallInsulationQualities);

        // Save the wall insulation
        $buildingElement = BuildingElement::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
            [
                'building_id' => $buildingId,
                'input_source_id' => $inputSourceId,
                'element_id' => $elementId,
            ],
            [
                'element_value_id' => $elementValueId,
            ]
        );

        $buildingFeature = BuildingFeature::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
            [
                'building_id' => $buildingId,
                'input_source_id' => $inputSourceId,
            ],
            [
                'facade_plastered_surface_id' => $plasteredWallSurface,
                'wall_joints' => $wallJoints,
                'cavity_wall' => $cavityWall,
                'contaminated_wall_joints' => $wallJointsContaminated,
                'wall_surface' => $wallSurface,
                'insulation_wall_surface' => $insulationWallSurface,
                'facade_damaged_paintwork_id' => $damagedPaintwork,
                'additional_info' => $additionalInfo,
                'facade_plastered_painted' => $facadePlasteredOrPainted,
            ]
        );



        // Save progress
        $this->saveAdvices($request);
        StepHelper::complete($this->step, $building, HoomdossierSession::getInputSource(true));
        StepDataHasBeenChanged::dispatch($this->step, $building, Hoomdossier::user());
        $cooperation = HoomdossierSession::getCooperation(true);

        $nextStep = StepHelper::getNextStep($this->step);
        $url = route($nextStep['route'], ['cooperation' => $cooperation]);

        if (! empty($nextStep['tab_id'])) {
            $url .= '#'.$nextStep['tab_id'];
        }

        return redirect($url);
    }

    protected function saveAdvices(Request $request)
    {
        $user = Building::find(HoomdossierSession::getBuilding())->user;
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
        $building = Building::find(HoomdossierSession::getBuilding());
        $user = $building->user;
        $userEnergyHabit = $user->energyHabit;

        $result = WallInsulation::calculate($building, $userEnergyHabit, $request->toArray());

        return response()->json($result);
    }
}
