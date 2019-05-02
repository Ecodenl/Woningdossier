<?php

namespace App\Http\Controllers\Cooperation\MyAccount;

use App\NotificationSetting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NotificationSettingsController extends Controller
{
    public function index()
    {
//        $notificationSettings = NotificationSetting

        return view('cooperation.my-account.notification-settings.index', compact('notificationSettings'));
    }
}
