<?php

namespace App\Http\Controllers\Cooperation\MyAccount;

use App\Helpers\HoomdossierSession;
use App\Http\Controllers\Controller;
use App\Models\BuildingPermission;
use App\Models\Cooperation;

class AccessController extends Controller
{
    public function index()
    {
        $buildingPermissions = BuildingPermission::where('building_id', HoomdossierSession::getBuilding())->get();

        return view('cooperation.my-account.access.index', compact('buildingPermissions'));
    }
}
