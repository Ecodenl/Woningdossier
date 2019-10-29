<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Helpers\HoomdossierSession;
use App\Helpers\StepHelper;
use App\Http\Controllers\Controller;
use App\Models\Step;
use Illuminate\Http\Request;

class VentilationController extends Controller
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
        $building = HoomdossierSession::getBuilding(true);

        $buildingVentilation = $building->getBuildingService('house-ventilation', HoomdossierSession::getInputSource(true));

        return view('cooperation.tool.ventilation.index', compact('building', 'buildingVentilation'));
       //return view('cooperation.tool.ventilation-information.index');
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
        // Save progress
        StepHelper::complete($this->step, $building, HoomdossierSession::getInputSource(true));
        $cooperation = HoomdossierSession::getCooperation(true);

        $nextStep = StepHelper::getNextStep($building, HoomdossierSession::getInputSource(true), $this->step);
        $url = $nextStep['url'];

        if (! empty($nextStep['tab_id'])) {
            $url .= '#'.$nextStep['tab_id'];
        }

        return redirect($url);
    }
}
