<?php

namespace App\Http\Controllers\Cooperation;

use App\Helpers\HoomdossierSession;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::active()
            ->forBuilding(HoomdossierSession::getBuilding(true))
            ->select('is_active', 'type')
            ->get();

        return response()->json(compact('notifications'));
    }
}
