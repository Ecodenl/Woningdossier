<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Helpers\Calculation\BankInterestCalculator;
use App\Helpers\Calculator;
use App\Helpers\Hoomdossier;
use App\Helpers\KeyFigures\WallInsulation\Temperature;
use App\Helpers\NumberFormatter;
use App\Http\Requests\WallInsulationRequest;
use App\Models\Building;
use App\Models\BuildingElement;
use App\Models\Cooperation;
use App\Models\Element;
use App\Models\ElementValue;
use App\Models\FacadeDamagedPaintwork;
use App\Models\FacadePlasteredSurface;
use App\Models\FacadeSurface;
use App\Models\MeasureApplication;
use App\Models\Step;
use App\Models\UserActionPlanAdvice;
use App\Models\UserInterest;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class WallInsulationController extends Controller
{


    protected $step;

    public function __construct(Request $request) {
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

        // get the next page order
        $nextPage = $this->step->order + 1;

        // check if the user is interested in wall insulation, if not redirect to next step
        if (Auth::user()->getInterestedType('element', 3)->interest_id > 3) {

            $nextStep = Step::where('order', $nextPage)->first();

            return redirect(url('tool/'.$nextStep->slug));
        }

    	$steps = Step::orderBy('order')->get();
        /** @var Building $building */
        $building = \Auth::user()->buildings()->first();
		// todo should use short here
        $facadeInsulation = $building->buildingElements()->where('element_id', 3)->first();
        $buildingFeature = $building->buildingFeatures;


        /** @var BuildingElement $houseInsulation */
        $surfaces = FacadeSurface::orderBy('order')->get();
        $facadePlasteredSurfaces = FacadePlasteredSurface::orderBy('order')->get();
        $facadeDamages = FacadeDamagedPaintwork::orderBy('order')->get();

        return view('cooperation.tool.wall-insulation.index', compact(
        	'steps', 'building', 'facadeInsulation',
	        'surfaces', 'buildingFeature',
            'facadePlasteredSurfaces', 'facadeDamages'
        ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(WallInsulationRequest $request)
    {
        // Get all the values from the form
        $wallInsulationQualities = $request->get('element', '');
        $plasteredWallSurface = $request->get('facade_plastered_surface_id', '');
        $damagedPaintwork = $request->get('facade_damaged_paintwork_id', 0);
        $wallJoints = $request->get('wall_joints', '');
        $wallJointsContaminated = $request->get('contaminated_wall_joints', '');
	    $wallSurface = $request->get('wall_surface', 0);
        $additionalInfo = $request->get('additional_info', '');
        $cavityWall = $request->get('cavity_wall', '');
        $facadePlasteredOrPainted = $request->get('facade_plastered_painted', '');

        // get the user buildingfeature
        $user = Auth::user();
        $building = $user->buildings()->first();
        $buildingFeatures = $building->buildingFeatures();

        // Element id's and values
        $elementId = key($wallInsulationQualities);
        $elementValueId = reset($wallInsulationQualities);

        // Save the wall insulation
        BuildingElement::updateOrCreate(
            [
                'building_id' => $building->id,
                'element_id' => $elementId
            ],
            [
                'element_value_id' => $elementValueId,
            ]
        );

        // Update the building feature table with some fresh data
        $buildingFeatures->update([
            'facade_plastered_surface_id' => $plasteredWallSurface,
            'wall_joints' => $wallJoints,
            'cavity_wall' => $cavityWall,
            'contaminated_wall_joints' => $wallJointsContaminated,
            'wall_surface' => $wallSurface,
            'facade_damaged_paintwork_id' => $damagedPaintwork,
            'additional_info' => $additionalInfo,
            'facade_plastered_painted' => $facadePlasteredOrPainted
        ]);

	    // Save progress
	    $this->saveAdvices($request);
	    \Auth::user()->complete($this->step);
        $cooperation = Cooperation::find(\Session::get('cooperation'));
        return redirect()->route('cooperation.tool.insulated-glazing.index', ['cooperation' => $cooperation]);
    }

    protected function saveAdvices(Request $request){
	    /** @var JsonResponse $results */
	    $results = $this->calculate($request);
	    $results = $results->getData(true);

		// Remove old results
		UserActionPlanAdvice::forMe()->forStep($this->step)->delete();

	    if (isset($results['insulation_advice']) && isset($results['cost_indication']) && $results['cost_indication'] > 0){
		    $measureApplication = MeasureApplication::translated('measure_name', $results['insulation_advice'], 'nl')->first(['measure_applications.*']);
		    if ($measureApplication instanceof MeasureApplication){
			    $actionPlanAdvice = new UserActionPlanAdvice($results);
			    $actionPlanAdvice->costs = $results['cost_indication']; // only outlier
				$actionPlanAdvice->user()->associate(Auth::user());
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

	    foreach($keysToMeasure as $key => $measureShort){
	    	if (isset($results[$key]['costs']) && $results[$key]['costs'] > 0){
				$measureApplication = MeasureApplication::where('short', $measureShort)->first();
				if ($measureApplication instanceof MeasureApplication) {
					$actionPlanAdvice = new UserActionPlanAdvice( $results[ $key ] );
					$actionPlanAdvice->user()->associate( Auth::user() );
					$actionPlanAdvice->measureApplication()->associate( $measureApplication );
					$actionPlanAdvice->step()->associate($this->step);
					$actionPlanAdvice->save();
				}
		    }
	    }

    }

    public function calculate(WallInsulationRequest $request){
    	/**
	     * @var Building $building
	     */
	    $user = \Auth::user();
	    $building = $user->buildings()->first();
	    $energyHabits = $user->energyHabit;

    	$cavityWall = $request->get('cavity_wall', -1);
		$elements = $request->get('element', []);
		//$facadeSurface = NumberFormatter::reverseFormat($request->get('wall_surface', 0));
	    $facadeSurface = $request->get('wall_surface', 0);

    	$result = [
    		'savings_gas' => 0,
		    'paint_wall' => [
		    	'costs' => 0,
			    'year' => 0,
		    ],
	    ];

	    $advice = Temperature::WALL_INSULATION_JOINTS;
    	if ($cavityWall == 1){
		    $advice = Temperature::WALL_INSULATION_JOINTS;
    	    $result['insulation_advice'] = trans('woningdossier.cooperation.tool.wall-insulation.insulation-advice.cavity-wall');
	    }
	    elseif ($cavityWall == 2){
    		$advice = Temperature::WALL_INSULATION_FACADE;
		    $result['insulation_advice'] = trans('woningdossier.cooperation.tool.wall-insulation.insulation-advice.facade-internal');
	    }
	    elseif($cavityWall == 0) {
		    $advice = Temperature::WALL_INSULATION_RESEARCH;
		    $result['insulation_advice'] = trans('woningdossier.cooperation.tool.wall-insulation.insulation-advice.research');
	    }

	    $elementValueId = array_shift($elements);
	    $elementValue = ElementValue::find($elementValueId);
	    if ($elementValue instanceof ElementValue){
			$result['savings_gas'] = Calculator::calculateGasSavings($building, $elementValue, $energyHabits, $facadeSurface, $advice);
	    }

	    $result['savings_co2'] = Calculator::calculateCo2Savings($result['savings_gas']);
	    $result['savings_money'] = round(Calculator::calculateMoneySavings($result['savings_gas']));
	    $result['cost_indication'] = Calculator::calculateCostIndication($facadeSurface, $advice);
	    $result['interest_comparable'] = NumberFormatter::format(BankInterestCalculator::getComparableInterest($result['cost_indication'], $result['savings_money']), 1);

	    $measureApplication = MeasureApplication::translated('measure_name', 'Reparatie voegwerk', 'nl')->first(['measure_applications.*']);
	    $surfaceId = $request->get('wall_joints', 1);
	    $wallJointsSurface = FacadeSurface::find($surfaceId);
	    $number = 0;
	    $year = null;
	    if ($wallJointsSurface instanceof FacadeSurface){
		    $number = $wallJointsSurface->calculate_value;
		    $year = Carbon::now()->year + $wallJointsSurface->term_years;
	    }
	    $costs = Calculator::calculateMeasureApplicationCosts($measureApplication, $number, $year);
	    $result['repair_joint'] = compact('costs', 'year');

	    $measureApplication = MeasureApplication::translated('measure_name', 'Reinigen metselwerk', 'nl')->first(['measure_applications.*']);
	    $surfaceId = $request->get('contaminated_wall_joints', 1);
	    $wallJointsSurface = FacadeSurface::find($surfaceId);
	    $number = 0;
	    $year = null;
	    if ($wallJointsSurface instanceof FacadeSurface){
		    $number = $wallJointsSurface->calculate_value;
		    $year = Carbon::now()->year + $wallJointsSurface->term_years;
	    }
	    $costs = Calculator::calculateMeasureApplicationCosts($measureApplication, $number, $year);
	    $result['clean_brickwork'] = compact('costs', 'year');

	    $measureApplication = MeasureApplication::translated('measure_name', 'Impregneren gevel', 'nl')->first(['measure_applications.*']);
	    $surfaceId = $request->get('contaminated_wall_joints', 1);
	    $wallJointsSurface = FacadeSurface::find($surfaceId);
	    $number = 0;
	    $year = null;
	    if ($wallJointsSurface instanceof FacadeSurface){
		    $number = $wallJointsSurface->calculate_value;
		    $year = Carbon::now()->year + $wallJointsSurface->term_years;
	    }
	    $costs = Calculator::calculateMeasureApplicationCosts($measureApplication, $number, $year);
	    $result['impregnate_wall'] = compact('costs', 'year');

	    // Note: this answer options are harcoded in template
	    $isPlastered = (int) $request->get('facade_plastered_painted', 2) != 2;

		if ($isPlastered) {
			$measureApplication     = MeasureApplication::translated( 'measure_name',
				'Gevelschilderwerk op stuk- of metselwerk',
				'nl' )->first( [ 'measure_applications.*' ] );
			$surfaceId              = $request->get( 'facade_plastered_surface_id' );
			$facadePlasteredSurface = FacadePlasteredSurface::find( $surfaceId );
			$damageId               = $request->get( 'facade_damaged_paintwork_id' );
			$facadeDamagedPaintwork = FacadeDamagedPaintwork::find( $damageId );
			$number                 = 0;
			$year                   = null;
			if ( $facadePlasteredSurface instanceof FacadePlasteredSurface && $facadeDamagedPaintwork instanceof FacadeDamagedPaintwork ) {
				$number = $facadePlasteredSurface->calculate_value;
				//$year = Carbon::now()->year + $facadePlasteredSurface->term_years;
				$year = Carbon::now()->year + $facadeDamagedPaintwork->term_years;
			}
			$costs                = Calculator::calculateMeasureApplicationCosts( $measureApplication,
				$number,
				$year );
			$result['paint_wall'] = compact( 'costs', 'year' );
		}

	    return response()->json($result);

    }

}
