<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Models\Building;
use App\Models\BuildingElement;
use App\Models\PresentWindow;
use App\Models\Step;
use App\Models\SurfacePaintedWall;
use App\Models\WallNeedImpregnation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class WallInsulationController extends Controller
{
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

        $houseInsulation = $building->buildingElements()->where('element_id', 3)->first();

        /** @var BuildingElement $houseInsulation */
        //dd($houseInsulation->element->values);

        //$houseInsulations = PresentWindow::all();
        $surfacePaintedWalls = SurfacePaintedWall::all();
        $wallsNeedImpregnation = WallNeedImpregnation::all();
        return view('cooperation.tool.wall-insulation.index', compact('steps', 'building', 'houseInsulation', 'surfacePaintedWalls', 'wallsNeedImpregnation'));
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
        
    }

    public function calculate(Request $request){

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
