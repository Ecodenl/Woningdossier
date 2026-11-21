<?php

namespace App\Http\Controllers\Cooperation\MyAccount;

use Illuminate\View\View;
use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Http\Controllers\Controller;
use App\Models\Cooperation;
use App\Models\NotificationInterval;
use App\Services\BuildingCoachStatusService;

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

        // For the access parts: every attached coach, not only those with a building permission. A
        // coach attached while the resident withholds access has no permission, but does sit in the
        // conversation, and this is the one place the resident can let them go.
        $connectedCoaches = BuildingCoachStatusService::getConnectedCoachesByBuilding($building, true)
            ->pluck('coach');

        return view('cooperation.my-account.index', compact(
            'user',
            'account',
            'building',
            'notificationIntervals',
            'notificationSettings',
            'connectedCoaches'
        ));
    }
}
