<?php

namespace App\Http\Controllers\Cooperation;

use Illuminate\View\View;
use App\Helpers\HoomdossierSession;
use App\Http\Controllers\Controller;
use App\Models\Cooperation;
use App\Models\Scan;

class HomeController extends Controller
{
    public function index(Cooperation $cooperation): View
    {
        $building = HoomdossierSession::getBuilding(true);
        $inputSource = HoomdossierSession::getInputSource(true);

        $scans = $cooperation->load(['scans' => fn($q) => $q->where('short', '!=', Scan::EXPERT)])->scans;

        return view('cooperation.home.index', compact('building', 'inputSource', 'scans'));
    }
}
