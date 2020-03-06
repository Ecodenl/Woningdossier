<?php

namespace App\Mail;

use App\Models\Account;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserChangedHisEmail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public $account;
    public $user;
    public $newMail;
    public $oldMail;

    /**
     * UserChangedHisEmail constructor.
     *
     * @param User    $user
     * @param Account $account
     * @param $newMail
     * @param $oldMail
     */
    public function __construct(User $user, Account $account, $newMail, $oldMail)
    {
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
            ->markdown('cooperation.mail.user.changed-email');
    }
}
