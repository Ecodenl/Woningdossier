<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Models\InsulatingGlazing;
use App\Models\InterestedToExecuteMeasure;
use App\Models\MovingPartsOfWindowAndDoorIsolated;
use App\Models\Step;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Lang;

class InsulatedGlazingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $steps = Step::orderBy('order')->get();

        $interestedToExecuteMeasures = InterestedToExecuteMeasure::all();
        $insulatedGlazings = InsulatingGlazing::all();
        $insulateQualities = MovingPartsOfWindowAndDoorIsolated::all();
        $keys = [
            'glass-in-lead',
            'place-hr-only-glass',
            'place-hr-with-frame',
            'triple-hr-glass'
        ];


        return view('cooperation.tool.insulated-glazing.index', compact('steps', 'interestedToExecuteMeasures', 'keys', 'insulatedGlazings', 'insulateQualities'));
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
