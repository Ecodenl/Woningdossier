<?php

namespace App\Mail;

use App\Models\Cooperation;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RequestAccountConfirmationEmail extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * @var User
     */
    public $user;

    /**
     * @var Cooperation
     */
    public $userCooperation;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, Cooperation $cooperation)
    {
        $this->user = $user;
        // use userCooperation instead of $cooperation. Because $cooperation
        // will be overridden by the view composer which would try to pull
        // $cooperation from the Session which is not present when the queue
        // driver is not equal to 'sync'
        $this->userCooperation = $cooperation;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject(strip_tags(__('cooperation/mail/confirm-account.subject')))
            ->view('cooperation.mail.user.confirm-account.view')
            ->view('cooperation.mail.user.confirm-account.text');
    }
}
