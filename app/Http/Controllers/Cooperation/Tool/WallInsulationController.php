<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Helpers\Calculation\BankInterestCalculator;
use App\Helpers\Calculator;
use App\Helpers\KeyFigures\WallInsulation\Temperature;
use App\Helpers\NumberFormatter;
use App\Http\Requests\WallInsulationRequest;
use App\Models\Building;
use App\Models\BuildingElement;
use App\Models\Cooperation;
use App\Models\ElementValue;
use App\Models\FacadeDamagedPaintwork;
use App\Models\FacadePlasteredSurface;
use App\Models\FacadeSurface;
use App\Models\MeasureApplication;
use App\Models\Step;
use App\Models\UserActionPlanAdvice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class WallInsulationController extends Controller
{

	public function __construct(Request $request) {
		$slug = str_replace('/tool/', '', $request->getRequestUri());
		$this->step = Step::where('slug', $slug)->first();
		$myStep = Step::where('slug', $this->step->slug)->first();
		$prev = Step::where('order', $myStep->order - 1)->first();
		if (!\Auth::user()->hasCompleted($prev)){
			return redirect('/tool/' . $prev->slug . '/')->with(['cooperation' => $request->get('cooperation')]);
		}
	}

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $steps = Step::orderBy('order')->get();
        /** @var Building $building */
        $building = \Auth::user()->buildings()->first();

        $facadeInsulation = $building->buildingElements()->where('element_id', 3)->first();
        $buildingFeature = $building->buildingFeatures;

        /** @var BuildingElement $houseInsulation */
        //dd($houseInsulation->element->values);

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
        $wallSurface = $request->get('facade_surface', '');
        $additionalInfo = $request->get('additional_info', '');
        $cavityWall = $request->get('cavity_wall', '');
        $facadePlasteredOrPainted = $request->get('facade_plastered_painted', '');
        foreach ($wallInsulationQualities as $wallInsulationQuality) {
            $wallInsulationQuality = $wallInsulationQuality;
        }

        // get the user buildingfeature
        $user = Auth::user();
        $building = $user->buildings()->first();
        $buildingFeatures = $building->buildingFeatures();

        // Update the building feature table with some fresh data
        $buildingFeatures->update([
            'element_values' => $wallInsulationQuality,
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
	    \Auth::user()->complete($this->step);
        $cooperation = Cooperation::find(\Session::get('cooperation'));
        return redirect()->route('cooperation.tool.insulated-glazing.index', ['cooperation' => $cooperation]);
    }

    public function calculate(Request $request){
	    /**
	     * @var Building $building
	     */
	    $user = \Auth::user();
	    $building = $user->buildings()->first();
	    $energyHabits = $user->energyHabit;

    	$cavityWall = $request->get('cavity_wall', -1);
		$elements = $request->get('element', []);
		$facadeSurface = $request->get('facade_surface', 0);

    	$result = [
    		'savings_gas' => 0,
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
	    if ($costs > 0) {
		    UserActionPlanAdvice::updateOrCreate( [
			    'user_id'                => Auth::user()->id,
			    'measure_application_id' => $measureApplication->id,
		    ],
			    [
				    'year' => $year,
			    ] );
	    }

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
		if ($costs > 0) {
			UserActionPlanAdvice::updateOrCreate( [
				'user_id'                => Auth::user()->id,
				'measure_application_id' => $measureApplication->id,
			],
				[
					'year' => $year,
				] );
		}

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
	    if ($costs > 0) {
		    UserActionPlanAdvice::updateOrCreate( [
			    'user_id'                => Auth::user()->id,
			    'measure_application_id' => $measureApplication->id,
		    ],
			    [
				    'year' => $year,
			    ] );
	    }

	    $measureApplication = MeasureApplication::translated('measure_name', 'Gevelschilderwerk op stuk- of metselwerk', 'nl')->first(['measure_applications.*']);
	    $surfaceId = $request->get('facade_plastered_surface_id', 1);
	    $facadePlasteredSurface = FacadePlasteredSurface::find($surfaceId);
	    $damageId = $request->get('facade_damaged_paintwork_id', 1);
	    $facadeDamagedPaintwork = FacadeDamagedPaintwork::find($damageId);
	    $number = 0;
	    $year = null;
	    if ($facadePlasteredSurface instanceof FacadePlasteredSurface && $facadeDamagedPaintwork instanceof FacadeDamagedPaintwork){
		    $number = $facadePlasteredSurface->calculate_value;
		    //$year = Carbon::now()->year + $facadePlasteredSurface->term_years;
		    $year = Carbon::now()->year + $facadeDamagedPaintwork->term_years;
	    }
	    $costs = Calculator::calculateMeasureApplicationCosts($measureApplication, $number, $year);
	    $result['paint_wall'] = compact('costs', 'year');
	    if ($costs > 0) {
		    UserActionPlanAdvice::updateOrCreate( [
			    'user_id'                => Auth::user()->id,
			    'measure_application_id' => $measureApplication->id,
		    ],
			    [
				    'year' => $year,
			    ] );
	    }

	    return response()->json($result);

    }

}
