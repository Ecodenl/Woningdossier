<?php

namespace App\Http\Controllers\Cooperation\Admin\Coach;

use App\Helpers\Hoomdossier;
use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Services\BuildingCoachStatusService;

class BuildingController extends Controller
{
    public function index()
    {
        $connectedBuildingsForUser = BuildingCoachStatusService::getConnectedBuildingsByUser(
            Hoomdossier::user()
        )->pluck('building_id');


        $buildings = Building::withRecentBuildingStatusInformation()
            ->whereIn('buildings.id', $connectedBuildingsForUser)
            ->orderByDesc('appointment_date')
            ->with('user')
            ->get();

        return view('cooperation.admin.coach.buildings.index', compact('buildings', 'buildings'));
    }
}

