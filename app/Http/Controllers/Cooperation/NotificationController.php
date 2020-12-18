<?php

namespace App\Http\Controllers\Cooperation;

use App\Services\NotificationService;
use App\Http\Controllers\Controller;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = NotificationService::getActiveNotifications();

        return response()->json(compact('notifications'));
    }
}
