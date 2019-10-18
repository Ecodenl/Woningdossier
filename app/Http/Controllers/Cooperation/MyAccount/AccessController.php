<?php

namespace App\Http\Controllers\Cooperation\MyAccount;

use App\Events\UserAllowedAccessToHisBuilding;
use App\Events\UserRevokedAccessToHisBuilding;
use App\Helpers\HoomdossierSession;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
