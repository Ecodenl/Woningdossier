<?php

namespace App\Http\Controllers\Cooperation\Admin\Coach;

use App\Helpers\Hoomdossier;
use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\BuildingCoachStatus;
use App\Models\Cooperation;
use App\Models\User;

class BuildingController extends Controller
{
    public function index(Cooperation $cooperation)
    {
        // get most recent building coach statuses for
        $buildingCoachStatuses = BuildingCoachStatus::hydrate(
            BuildingCoachStatus::getConnectedBuildingsByUser(Hoomdossier::user(), $cooperation)->all()
        );

        return view('cooperation.admin.coach.buildings.index', compact('buildings', 'buildingCoachStatuses'));
    }
}
