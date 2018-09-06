<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Helpers\StepHelper;
use App\Models\BuildingCurrentHeating;
use App\Models\Cooperation;
use App\Models\HeatSource;
use App\Models\PresentHeatPump;
use App\Models\Step;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class HeatPumpController extends Controller
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

        // the element ids for this page
        $serviceIds = [1, 2];

        // create empty array for the interestedIds
        $interestedIds = [];

        // the interest ids that people select when they do not have any interest
        $noInterestIds = [4, 5];

        // go through the serviceid and get the user interest id to put them into the array
        foreach ($serviceIds as $serviceId) {
            array_push($interestedIds, Auth::user()->getInterestedType('service', $serviceId)->interest_id);
        }
        // check if the user wants to do something with there glazings

        if ($interestedIds == array_intersect($interestedIds, $noInterestIds)) {

            $nextStep = Step::where('order', $nextPage)->first();

            return redirect(url('tool/'.$nextStep->slug));
        }

        $heatpumpTypes = PresentHeatPump::all();
        $buildingCurrentHeatings = BuildingCurrentHeating::all();
        $heatSources = HeatSource::all();
        $steps = Step::orderBy('order')->get();
        return view('cooperation.tool.heat-pump.index', compact('heatpumpTypes', 'steps', 'heatSources', 'buildingCurrentHeatings'));
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Auth::user()->complete($this->step);
        $cooperation = Cooperation::find($request->session()->get('cooperation'));

        return redirect()->route(StepHelper::getNextStep($this->step), ['cooperation' => $cooperation]);
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
