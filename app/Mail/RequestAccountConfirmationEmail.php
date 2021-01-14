<?php

namespace App\Mail;

use App\Models\Cooperation;
use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RequestAccountConfirmationEmail extends Mailable
{
    use SerializesModels;

    /**
     * @var User
     */
    public $user;

    /**
     * @var Cooperation
     */
    public $userCooperation;

    public $verifyUrl;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, string $verifyUrl)
    {
        $this->user = $user;
        $this->verifyUrl = $verifyUrl;
        // use userCooperation instead of $cooperation. Because $cooperation
        // will be overridden by the view composer which would try to pull
        // $cooperation from the Session which is not present when the queue
        // driver is not equal to 'sync'
        $this->userCooperation = $user->cooperation;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->to($this->user->account->email, sprintf('%s %s', $this->user->first_name, $this->user->last_name))
            ->subject(strip_tags(__('cooperation/mail/confirm-account.subject')))
            ->view('cooperation.mail.user.confirm-account.view')
            ->text('cooperation.mail.user.confirm-account.text');
    }
}
