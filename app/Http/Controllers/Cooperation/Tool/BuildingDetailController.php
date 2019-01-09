<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Helpers\HoomdossierSession;
use App\Models\Building;
use App\Models\Cooperation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BuildingDetailController extends Controller
{
    public function index(Request $request, Cooperation $cooperation)
    {
        $building = Building::find(HoomdossierSession::getBuilding());
        return view('cooperation.tool.building-detail.index', compact('building'));
    }

    public function store(Request $request, Cooperation $cooperation)
    {

    }
}
