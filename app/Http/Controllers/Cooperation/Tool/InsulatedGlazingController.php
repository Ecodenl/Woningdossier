<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Http\Requests\InsulatedGlazingFormRequest;
use App\Models\Cooperation;
use App\Models\DamageToPaintWork;
use App\Models\HouseFrame;
use App\Models\InsulatingGlazing;
use App\Models\InterestedToExecuteMeasure;
use App\Models\MovingPartsOfWindowAndDoorIsolated;
use App\Models\Step;
use App\Models\WoodElement;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Lang;

class InsulatedGlazingController extends Controller
{
    /**
     * Display a listing of the resource.s
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

        $woodElements = WoodElement::all();
        //$damageToPaintWorks = DamageToPaintWork::all();
	    $damageToPaintWorks = [];
        $houseFrames = HouseFrame::all();


        return view('cooperation.tool.insulated-glazing.index', compact('steps', 'interestedToExecuteMeasures', 'keys',
            'insulatedGlazings', 'insulateQualities', 'woodElements', 'damageToPaintWorks', 'houseFrames'
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
