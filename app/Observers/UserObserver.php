<?php

namespace App\Observers;

use App\Models\Account;
use App\Models\NotificationInterval;
use App\Models\NotificationType;
use App\Models\User;
use Carbon\Carbon;

class UserObserver
{
    public function saving(User $user): void
    {
        // Not allowed as null
        $user->phone_number ??= '';
    }

    public function created(User $user): void
    {
        // we create for every notification type a setting with daily interval and set the last_notified_at to now
        $notificationTypes = NotificationType::all();
        $interval = NotificationInterval::where('short', 'daily')->first();

        foreach ($notificationTypes as $notificationType) {
            $user->notificationSettings()->create([
                'type_id'          => $notificationType->id,
                'interval_id'      => $interval->id,
                'last_notified_at' => Carbon::now(),
            ]);
        }
    }

    public function updated(User $user): void
    {
        // Wiping the account for these trivial columns? No thanks.
        $ignore = [
            'regulations_refreshed_at',
            'last_visited_url',
            'updated_at',
        ];

        if (! empty(array_diff(array_keys($user->getDirty()), $ignore))) {
            \App\Helpers\Cache\Account::wipe($user->account);
        }
    }

    public function deleted(User $user): void
    {
        if ($user->account instanceof Account) {
            \App\Helpers\Cache\Account::wipe($user->account);
        }
    }
}
