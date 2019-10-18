<?php

namespace App\Http\Controllers\Cooperation\MyAccount;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cooperation\MyAccount\NotificationSettingsFormRequest;
use App\Models\Cooperation;
use App\NotificationSetting;

class NotificationSettingsController extends Controller
{
    public function update(NotificationSettingsFormRequest $request, Cooperation $cooperation, $notificationSettingId)
    {
        $intervalId = $request->input('notification_setting.'.$notificationSettingId.'.interval_id', null);

        NotificationSetting::where('id', $notificationSettingId)->update([
            'interval_id' => $intervalId,
        ]);

        return redirect()->route('cooperation.my-account.index')
                         ->with('success', __('my-account.notification-settings.update.success'));
    }
}
