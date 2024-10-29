<?php

namespace App\Http\Controllers\Cooperation\MyAccount;

use Illuminate\View\View;
use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Http\Controllers\Controller;
use App\Models\BuildingPermission;
use App\Models\Cooperation;
use App\Models\NotificationInterval;

class MyAccountController extends Controller
{
    public function index(Cooperation $cooperation): View
    {
        // for the account / user settings
        $user = Hoomdossier::user();
        $account = Hoomdossier::account();
        $building = HoomdossierSession::getBuilding(true);

        // for the notification settings
        $notificationSettings = $user->notificationSettings;
        $notificationIntervals = NotificationInterval::all();

        // for the access parts
        $buildingPermissions = BuildingPermission::where('building_id', $building->id)->get();

        return view('cooperation.my-account.index', compact(
            'user', 'account', 'building', 'notificationIntervals', 'notificationSettings', 'buildingPermissions'
        ));
    }
}
