<?php

namespace App\Http\Controllers\Cooperation\MyAccount;

use Illuminate\View\View;
use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Http\Controllers\Controller;
use App\Models\Cooperation;

class TwoFactorAuthenticationController extends Controller
{

    public function index(Cooperation $cooperation): View
    {
        // for the account / user settings
        $user = Hoomdossier::user();
        $account = Hoomdossier::account();
        $building = HoomdossierSession::getBuilding(true);

        return view('cooperation.my-account.two-factor-authentication.index', compact(
            'user',
            'account',
            'building',
        ));
    }
}
