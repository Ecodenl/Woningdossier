<?php

namespace App\Http\Controllers\Cooperation;

use App\Helpers\HoomdossierSession;
use App\Helpers\QuickScanHelper;
use App\Http\Controllers\Controller;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\Scan;
use App\Models\Step;
use App\Models\SubStep;
use App\Services\Scans\ScanFlowService;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;

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
        $building = HoomdossierSession::getBuilding(true);
        $inputSource = HoomdossierSession::getInputSource(true);

        return view('cooperation.home.index', compact('building', 'inputSource'));
    }
}
