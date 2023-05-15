<?php

namespace App\Http\Controllers\Cooperation;

use App\Helpers\HoomdossierSession;
use App\Http\Controllers\Controller;
use App\Jobs\CheckBuildingAddress;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\MeasureApplication;
use App\Models\RelatedModel;
use App\Models\Scan;
use App\Services\RelatedModelService;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @param  \App\Models\Cooperation  $cooperation
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Cooperation $cooperation)
    {
        CheckBuildingAddress::dispatchSync(Building::first());
        $building = HoomdossierSession::getBuilding(true);
        $inputSource = HoomdossierSession::getInputSource(true);

        $scans = $cooperation->load(['scans' => fn($q) => $q->where('short', '!=', Scan::EXPERT)])->scans;

        return view('cooperation.home.index', compact('building', 'inputSource', 'scans'));
    }
}
