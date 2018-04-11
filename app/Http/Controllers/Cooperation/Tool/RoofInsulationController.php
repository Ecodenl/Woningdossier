<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Models\Building;
use App\Models\BuildingElement;
use App\Models\BuildingFeature;
use App\Models\BuildingHeating;
use App\Models\Quality;
use App\Models\RoofTileStatus;
use App\Models\RoofType;
use App\Models\Step;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RoofInsulationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
	    /** @var Building $building */
	    $building = \Auth::user()->buildings()->first();

		/** @var BuildingFeature $features */
	    $features = $building->buildingFeatures;
	    $roofTypes = RoofType::all();
	    $steps = Step::orderBy('order')->get();
	    $currentRoofTypes = $building->roofTypes;
	    $roofTileStatuses = RoofTileStatus::all();


	    //$buildingInsulation = $building->getBuildingElement('roof-insulation');
	    //$roofInsulation = $buildingInsulation instanceof BuildingElement ? $buildingInsulation->element : null;

        //$qualities = Quality::all();

        $heatings = BuildingHeating::all();

        // If the answer's modal is present replace the answer with the model thing thing
        $answer = 'Waarde.';


        return view('cooperation.tool.roof-insulation.index', compact(
        	'roofTypes', 'currentRoofTypes', 'features',
        	'steps',
        	'answer', 'heatings'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

}
