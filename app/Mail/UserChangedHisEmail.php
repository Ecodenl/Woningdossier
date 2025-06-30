<?php

namespace App\Mail;

use App\Helpers\Queue;
use App\Models\Account;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserChangedHisEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public Account $account,
        public string $newMail,
        public string $oldMail
    )
    {
        $this->onQueue(Queue::APP_EXTERNAL);
    }

    /**
     * Build the message.
     */
    public function build(): static
    {
        return $this
            ->subject(strip_tags(__('cooperation/mail/changed-email.subject')))
            ->view('cooperation.mail.user.changed-email.view')
            ->text('cooperation.mail.user.changed-email.text');
    }
}
