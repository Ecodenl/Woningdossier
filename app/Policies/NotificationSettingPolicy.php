<?php

namespace App\Policies;

use App\Models\User;
use App\NotificationSetting;
use Illuminate\Auth\Access\HandlesAuthorization;

class NotificationSettingPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if a user can see a notification setting
     *
     * @param  User  $user
     * @param  NotificationSetting  $notificationSetting
     *
     * @return bool
     */
    public function show(User $user, NotificationSetting $notificationSetting)
    {
        return $user->notificationSettings()->find($notificationSetting->id) instanceof NotificationSetting;
    }

    /**
     * Determine if a user can update a notification setting
     *
     * @param  User  $user
     * @param  NotificationSetting  $notificationSetting
     *
     * @return bool
     */
    public function update(User $user, NotificationSetting $notificationSetting)
    {
        return $user->notificationSettings()->find($notificationSetting->id) instanceof NotificationSetting;
    }
}
