<?php

namespace App\Services;

use App\Helpers\HoomdossierSession;
use App\Models\Notification;

class NotificationService
{
    public static function getActiveNotifications ()
    {
        return Notification::active()
            ->forBuilding(HoomdossierSession::getBuilding(true))
            ->select('is_active', 'type')
            ->get();
    }
}