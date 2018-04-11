<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Http\Requests\FloorInsulationFormRequest;
use App\Models\Building;
use App\Models\CrawlSpaceHeight;
use App\Models\Quality;
use App\Models\Step;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;

class FloorInsulationController extends Controller
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
	    // todo should use short here
		$floorInsulation = $building->buildingElements()->where('element_id', 4)->first();


        $insulations = Quality::all();
        $steps = Step::orderBy('order')->get();
        $crawlHeights = CrawlSpaceHeight::all();
        return view('cooperation.tool.floor-insulation.index', compact(
            'floorInsulation',
            'steps', 'crawlHeights'
        ));
    }

    public function calculate(Request $request){

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param FloorInsulationFormRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(FloorInsulationFormRequest $request)
    {
        // Get the value's from the input's
        $floorInsulation = $request->floor_insulation;
        $hasCrawlspace = $request->has_crawlspace;
        $hasCrawlspaceAccess = $request->crawlspace_access;
        $crawlspaceHeight = $request->crawlspace_height;
        $floorSurface = $request->floor_surface;

        // TODO: store the request
        return redirect()->route('cooperation.tool.roof-insulation.index', ['cooperation' => App::make('Cooperation')]);
    }

}
