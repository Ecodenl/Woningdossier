<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Helpers\HoomdossierSession;
use App\Models\BuildingCurrentHeating;
use App\Models\HeatSource;
use App\Models\PresentHeatPump;
use Illuminate\Http\Request;

class HeatPumpController extends ToolController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $heatpumpTypes = PresentHeatPump::all();
        $buildingCurrentHeatings = BuildingCurrentHeating::all();
        $heatSources = HeatSource::all();

        return view('cooperation.tool.heat-pump.index', compact('heatpumpTypes', 'heatSources', 'buildingCurrentHeatings'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $building = HoomdossierSession::getBuilding(true);
        $inputSource = HoomdossierSession::getInputSource(true);

        return $this->completeStore($this->step, $building, $inputSource);
    }
}
