<?php

namespace App\Http\Controllers\Cooperation;

use Illuminate\View\View;
use App\Helpers\HoomdossierSession;
use App\Helpers\ScanAvailabilityHelper;
use App\Http\Controllers\Controller;
use App\Models\Cooperation;
use App\Models\Scan;

class HomeController extends Controller
{
    public function index(Cooperation $cooperation): View
    {
        $building = HoomdossierSession::getBuilding(true);
        $inputSource = HoomdossierSession::getInputSource(true);

        $scans = Scan::simpleScans()->get()
            ->filter(fn ($scan) => ScanAvailabilityHelper::isAvailableForBuilding($building, $scan))
            ->values();

        return view('cooperation.home.index', compact('building', 'inputSource', 'scans'));
    }
}
