<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Helpers\StepHelper;
use App\Http\Controllers\Controller;
use App\Models\BuildingCurrentHeating;
use App\Models\Cooperation;
use App\Models\HeatSource;
use App\Models\PresentHeatPump;
use App\Models\Step;
use Illuminate\Http\Request; use App\Scopes\GetValueScope;
use Illuminate\Support\Facades\Auth;

class HeatPumpController extends Controller
{
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
        // get the next page order
        $nextPage = $this->step->order + 1;

        $heatpumpTypes = PresentHeatPump::all();
        $buildingCurrentHeatings = BuildingCurrentHeating::all();
        $heatSources = HeatSource::all();


        return view('cooperation.tool.heat-pump.index', compact('heatpumpTypes',  'heatSources', 'buildingCurrentHeatings'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
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
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
    }
}
