<?php

namespace App\Listeners;

use App\Mail\UserChangedHisEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Str;

class SetOldEmailListener implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Handle the changed email event.
     *
     * Update data and send a email recovery mail to the user his old email address.
     *
     * @param object $event
     *
     * @return void
     */
    public function handle($event)
    {
        $account = $event->account;
        $user = $event->user;

        $newMail = $event->newEmail;
        $oldEmail = $event->oldEmail;

        // set the data
        $account->old_email = $oldEmail;
        $account->old_email_token = hash_hmac('sha256', Str::random(40), 10);
        $account->save();

        \Mail::to($oldEmail)->sendNow(new UserChangedHisEmail($user, $account, $newMail, $oldEmail));
    }
}
