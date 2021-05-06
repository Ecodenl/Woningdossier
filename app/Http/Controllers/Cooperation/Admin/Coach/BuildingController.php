<?php

namespace App\Http\Controllers\Cooperation\Admin\Coach;

use App\Helpers\Hoomdossier;
use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\Cooperation;
use App\Services\BuildingCoachStatusService;

class BuildingController extends Controller
{
    public function index(Cooperation $cooperation)
    {
        $connectedBuildingsForUser = BuildingCoachStatusService::getConnectedBuildingsByUser(Hoomdossier::user())->pluck('building_id');

        $buildings = Building::whereIn('buildings.id', $connectedBuildingsForUser)
           ->with([
               'user',
               'buildingStatuses' => function ($query) {
                   $query->with('status');
               }]
           )->get();


        return view('cooperation.admin.coach.buildings.index', compact('buildings', 'buildings'));
    }
}


