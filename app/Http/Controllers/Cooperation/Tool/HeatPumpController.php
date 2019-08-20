<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Helpers\HoomdossierSession;
use App\Helpers\StepHelper;
use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\BuildingCurrentHeating;
use App\Models\Cooperation;
use App\Models\HeatSource;
use App\Models\PresentHeatPump;
use App\Models\Step;
use Illuminate\Http\Request;

class HeatPumpController extends Controller
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
        $heatpumpTypes = PresentHeatPump::all();
        $buildingCurrentHeatings = BuildingCurrentHeating::all();
        $heatSources = HeatSource::all();

        return view('cooperation.tool.heat-pump.index', compact('heatpumpTypes', 'heatSources', 'buildingCurrentHeatings'));
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
        $building = HoomdossierSession::getBuilding(true);
        StepHelper::complete($this->step, $building, HoomdossierSession::getInputSource(true));
        $cooperation = HoomdossierSession::getCooperation(true);

        $nextStep = StepHelper::getNextStep($this->step);
        $url = route($nextStep['route'], ['cooperation' => $cooperation]);

        if (! empty($nextStep['tab_id'])) {
            $url .= '#'.$nextStep['tab_id'];
        }

        return redirect($url);
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
