<?php

namespace App\Mail;

use App\Models\Account;
use App\Models\Cooperation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPasswordRequest extends Mailable
{
    use Queueable;
    use SerializesModels;

    public $userCooperation;
    public $email;
    public $account;
    public $user;
    public $token;

    /**
     * Create new message instance.
     *
     * UserCreatedEmail constructor.
     */
    public function __construct(Cooperation $cooperation, Account $account, $token)
    {
        $this->token = $token;
        $this->email = encrypt($account->email);
        $this->account = $account;
        $this->user = $account->user();
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
            ->to($this->account->email, sprintf('%s %s', $this->user->first_name, $this->user->last_name))
            ->subject(strip_tags(__('cooperation/mail/reset-password.subject')))
            ->view('cooperation.mail.user.password-reset-request.view')
            ->text('cooperation.mail.user.password-reset-request.text');
    }
}
