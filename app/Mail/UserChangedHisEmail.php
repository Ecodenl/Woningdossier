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

    public $account;
    public $user;
    public $newMail;
    public $oldMail;

    /**
     * UserChangedHisEmail constructor.
     *
     * @param $newMail
     * @param $oldMail
     */
    public function __construct(User $user, Account $account, $newMail, $oldMail)
    {
        $this->onQueue(Queue::APP_EXTERNAL);
        $this->user = $user;
        $this->account = $account;
        $this->newMail = $newMail;
        $this->oldMail = $oldMail;
    }

    /**
     * Build the message.
     *
     * @return $thi
     */
    public function build()
    {
        return $this
            ->subject(strip_tags(__('cooperation/mail/changed-email.subject')))
            ->view('cooperation.mail.user.changed-email.view')
            ->text('cooperation.mail.user.changed-email.text');
    }
}
