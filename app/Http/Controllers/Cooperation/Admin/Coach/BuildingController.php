<?php

namespace App\Http\Controllers\Cooperation\Admin\Coach;

use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\BuildingCoachStatus;
use App\Models\BuildingPermission;
use App\Models\Cooperation;
use App\Models\PrivateMessage;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BuildingController extends Controller
{
    public function index()
    {

        $userId = \Auth::id();

        // get most recent building coach statuses for
        $buildingCoachStatuses = BuildingCoachStatus::hydrate(
            BuildingCoachStatus::getConnectedBuildingsByUserId($userId)->all()
        );

        return view('cooperation.admin.coach.buildings.index', compact('buildings', 'buildingCoachStatuses'));
    }
}
