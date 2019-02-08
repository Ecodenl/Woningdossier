<?php

namespace App\Http\Controllers\Cooperation\MyAccount;

use App\Helpers\HoomdossierSession;
use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\BuildingPermission;
use App\Models\Cooperation;
use App\Services\BuildingCoachStatusService;
use App\Services\BuildingPermissionService;
use Illuminate\Http\Request;

class AccessController extends Controller
{
    public function index()
    {
        $buildingPermissions = BuildingPermission::where('building_id', HoomdossierSession::getBuilding())->get();

        return view('cooperation.my-account.access.index', compact('buildingPermissions'));
    }

}
