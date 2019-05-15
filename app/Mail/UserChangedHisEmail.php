<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserChangedHisEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $newMail;
    public $oldMail;

    /**
     * Create a new message instance.
     *
     * @param  User  $user
     * @param $newMail
     * @param $oldMail
     */
    public function __construct(User $user, $newMail, $oldMail)
    {
        $this->user = $user;
        $this->newMail = $newMail;
        $this->oldMail = $oldMail;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject(__('mail.changed-email.subject'))
            ->view('cooperation.mail.user.changed-email');
    }
}
