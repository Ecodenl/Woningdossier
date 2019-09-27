<?php

namespace App\Http\Controllers\Cooperation\MyAccount;

use App\Events\UserAllowedAccessToHisBuilding;
use App\Events\UserRevokedAccessToHisBuilding;
use App\Helpers\HoomdossierSession;
use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\BuildingCoachStatus;
use App\Models\BuildingPermission;
use App\Models\PrivateMessage;
use App\Models\User;
use App\Services\BuildingCoachStatusService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class AccessController extends Controller
{
    public function allowAccess(Request $request)
    {
        $building = HoomdossierSession::getBuilding(true);

        if ($request->has('allow_access')) {
            UserAllowedAccessToHisBuilding::dispatch($building);
        } else {
            UserRevokedAccessToHisBuilding::dispatch($building);
        }

        return redirect()->back();
    }

}
