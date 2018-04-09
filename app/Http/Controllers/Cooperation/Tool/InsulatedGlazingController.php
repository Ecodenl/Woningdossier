<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Http\Requests\InsulatedGlazingFormRequest;
use App\Models\Building;
use App\Models\BuildingHeating;
use App\Models\BuildingInsulatedGlazing;
use App\Models\Cooperation;
//use App\Models\DamageToPaintWork;
use App\Models\Element;
use App\Models\HouseFrame;
use App\Models\InsulatingGlazing;
use App\Models\Interest;
use App\Models\MeasureApplication;
use App\Models\MovingPartsOfWindowAndDoorIsolated;
use App\Models\Step;
use App\Models\UserInterest;
use App\Models\WoodElement;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class InsulatedGlazingController extends Controller
{
    /**
     * Display a listing of the resource.s
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
	    /**
	     * @var Building $building
	     */
    	$building = \Auth::user()->buildings->first();
        $steps = Step::orderBy('order')->get();

	    $interests = Interest::orderBy('order')->get();

        $insulatedGlazings = InsulatingGlazing::all();
		$crackSealing = Element::where('short', 'crack-sealing')->first();
		$frames = Element::where('short', 'frames')->first();
		$woodElements = Element::where('short', 'wood-elements')->first();

        //$insulateQualities = MovingPartsOfWindowAndDoorIsolated::all();
		$heatings = BuildingHeating::all();

		// nl names
		$measureApplicationNames = [
			'Glas in lood vervangen',
			'Plaatsen van HR++ glas (alleen het glas)',
			'Plaatsen van HR++ glas (inclusief kozijn)',
			'Plaatsen van drievoudige HR beglazing (inclusief kozijn)',
		];

		$buildingInsulatedGlazings = [];
	    $userInterests = [];

		foreach($measureApplicationNames as $measureApplicationName){
			$measureApplication = MeasureApplication::translated('measure_name', $measureApplicationName, 'nl')->first(['measure_applications.*']);

			if ($measureApplication instanceof MeasureApplication) {
				// get current situation
				$currentInsulatedGlazing = $building->currentInsulatedGlazing()->where('measure_application_id', $measureApplication->id)->first();
				if ($currentInsulatedGlazing instanceof BuildingInsulatedGlazing){
					$buildingInsulatedGlazings[$measureApplication->id] = $currentInsulatedGlazing;
				}
				// get interests for the measure
				$measureInterest = \Auth::user()->interests()
				                                ->where('interested_in_type', 'measure_application')
												->where('interested_in_id', $measureApplication->id)
				                                ->get();
				if ($measureInterest instanceof UserInterest){
					// We only have to check on the interest ID, so we don't put
					// full objects in the array
					$userInterests[$measureApplication->id] = $measureInterest->interest_id;
				}

				$measureApplications [] = $measureApplication;
			}
		}

        //$woodElements = WoodElement::all();
        //$damageToPaintWorks = DamageToPaintWork::all();
	    $damageToPaintWorks = [];

        return view('cooperation.tool.insulated-glazing.index', compact(
        	'building', 'steps', 'interests',
            'heatings', 'measureApplications', 'insulatedGlazings', 'buildingInsulatedGlazings',
	        'userInterests', 'crackSealing', 'frames', 'woodElements',
	        'damageToPaintWorks'
        ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }


    /**
     * Store the incoming request and redirect to the next step.
     *
     * @param InsulatedGlazingFormRequest   $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(InsulatedGlazingFormRequest $request)
    {
        $cooperation = Cooperation::all();
        $steps = Step::orderBy('order')->get();

        return redirect()->route('cooperation.tool.floor-insulation.index', ['cooperation' => $cooperation]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
