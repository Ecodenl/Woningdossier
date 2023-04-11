<?php

namespace App\Http\Controllers\Cooperation\MyAccount;

use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Http\Controllers\Controller;
use App\Models\BuildingPermission;
use App\Models\Cooperation;
use App\Models\NotificationInterval;

class TwoFactorAuthenticationController extends Controller
{

    public function index(Cooperation $cooperation)
    {
        // for the account / user settings
        $user = Hoomdossier::user();
        $account = Hoomdossier::account();
        $building = HoomdossierSession::getBuilding(true);

        return view('cooperation.my-account.two-factor-authentication.index', compact(
            'user', 'account', 'building',
        ));
    }

}
