<?php

namespace App\Http\Controllers\Cooperation\MyAccount;

use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Http\Controllers\Controller;
use App\Models\Cooperation;

class MyAccountController extends Controller
{
    public function index(Cooperation $cooperation)
    {
        $user = Hoomdossier::user();
        $account = Hoomdossier::account();
        $building = HoomdossierSession::getBuilding(true);

        return view('cooperation.my-account.index', compact('user', 'account', 'building'));
    }
}
