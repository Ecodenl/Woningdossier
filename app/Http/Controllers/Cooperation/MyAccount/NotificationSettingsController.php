<?php

namespace App\Http\Controllers\Cooperation\MyAccount;

use App\Http\Requests\Cooperation\MyAccount\NotificationSettingsFormRequest;
use App\Models\Cooperation;
use App\Models\NotificationInterval;
use App\NotificationSetting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NotificationSettingsController extends Controller
{
    public function index()
    {
        $notificationSettings = \App\Helpers\Hoomdossier::user()->notificationSettings;
        $notificationIntervals = NotificationInterval::all();


        return view('cooperation.my-account.notification-settings.index', compact('notificationSettings', 'notificationIntervals'));
    }
    public function update(NotificationSettingsFormRequest $request, Cooperation $cooperation, $notificationSettingId)
    {
        $intervalId = $request->input('notification_setting.'.$notificationSettingId.'.interval_id', null);

        NotificationSetting::where('id', $notificationSettingId)->update([
            'interval_id' => $intervalId
        ]);

        return redirect()->route('cooperation.my-account.notification-settings.index')
                         ->with('success', __('my-account.notification-settings.update.success'));
    }
}
