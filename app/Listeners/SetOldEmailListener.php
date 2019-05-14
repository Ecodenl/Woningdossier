<?php

namespace App\Listeners;

use App\Mail\UserChangedHisEmail;
use Illuminate\Support\Str;

class SetOldEmailListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the changed email event
     *
     * Update data and send a email recovery mail to the user his old email address.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $user = $event->user;
        $newMail = $event->newEmail;
        $oldEmail = $event->oldEmail;

        // set the data
        $user->old_email = $oldEmail;
        $user->old_email_token = hash_hmac('sha256', Str::random(40), 10);
        $user->save();

        \Mail::to($oldEmail)->sendNow(new UserChangedHisEmail($user, $newMail, $oldEmail));
    }
}
