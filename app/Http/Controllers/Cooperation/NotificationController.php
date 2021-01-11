<?php

namespace App\Http\Controllers\Cooperation;

use App\Helpers\HoomdossierSession;
use App\Http\Controllers\Controller;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::activeNotifications(
            HoomdossierSession::getBuilding(true),
            HoomdossierSession::getInputSource(true)
        )->get();

        return response()->json(compact('notifications'));
    }
}
