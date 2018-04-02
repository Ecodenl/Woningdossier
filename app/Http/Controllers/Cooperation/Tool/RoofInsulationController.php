<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Models\BuildingHeating;
use App\Models\Quality;
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
        $qualities = Quality::all();
        $roofTypes = RoofType::all();
        $steps = Step::orderBy('order')->get();
        $heatings = BuildingHeating::all();

        // If the answer's modal is present replace the answer with the model thing thing
        $answer = 'Waarde.';


        return view('cooperation.tool.roof-insulation.index', compact('qualities', 'roofTypes', 'steps', 'answer', 'heatings'));
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
        //
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
