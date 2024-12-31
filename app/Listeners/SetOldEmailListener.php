<?php

namespace App\Listeners;

use App\Events\UserChangedHisEmailEvent;
use App\Helpers\Queue;
use App\Mail\UserChangedHisEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class SetOldEmailListener implements ShouldQueue
{
    public $queue = Queue::APP;

    /**
     * Handle the changed email event.
     *
     * Update data and send a email recovery mail to the user his old email address.
     */
    public function handle(UserChangedHisEmailEvent $event): void
    {
        $account = $event->account;
        $user = $event->user;

        $newMail = $event->newEmail;
        $oldEmail = $event->oldEmail;

        // set the data
        $account->old_email = $oldEmail;
        $account->old_email_token = hash_hmac('sha256', Str::random(40), 10);
        $account->save();

        Mail::to($oldEmail)->send(new UserChangedHisEmail($user, $account, $newMail, $oldEmail));
    }
}
