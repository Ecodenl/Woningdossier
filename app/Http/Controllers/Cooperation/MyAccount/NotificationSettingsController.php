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

        return redirect()->back();
        $notificationSettings = \Auth::user()->notificationSettings;

        return view('cooperation.my-account.notification-settings.index', compact('notificationSettings'));
    }

    public function show(Cooperation $cooperation, $notificationSettingId)
    {
        return redirect()->back();
        $notificationSetting = NotificationSetting::find($notificationSettingId);
        $notificationIntervals = NotificationInterval::all();

        $this->authorize('show', $notificationSetting);

        return view('cooperation.my-account.notification-settings.show', compact(
            'notificationSetting', 'notificationIntervals'
        ));
    }

    public function update(NotificationSettingsFormRequest $request, Cooperation $cooperation, $notificationSettingId)
    {
        $notificationSetting = NotificationSetting::find($notificationSettingId);
        $intervalId = $request->input('notification_setting.interval_id', null);

        $this->authorize('update', $notificationSetting);

        NotificationSetting::where('id', $notificationSettingId)->update([
            'interval_id' => $intervalId
        ]);

        return redirect()->route('cooperation.my-account.notification-settings.index')
                         ->with('success', __('my-account.notification-settings.update.success'));
    }
}
