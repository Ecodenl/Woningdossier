<?php

namespace App\Mail;

use App\Models\Account;
use App\Models\Cooperation;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ResetPasswordRequest extends Mailable
{
    use Queueable, SerializesModels;

    public $userCooperation;
    public $email;
    public $account;
    public $token;

    /**
     * Create new message instance.
     *
     * UserCreatedEmail constructor.
     *
     * @param Cooperation $cooperation
     */
    public function __construct(Cooperation $cooperation, Account $account, $token)
    {
        $this->token = $token;
        $this->email = encrypt($account->email);
        $this->account = $account;
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
            ->to($this->account->email, sprintf("%s %s", $this->account->user()->first_name, $this->account->user()->last_name))
            ->subject(__('mail.reset_password.subject'))
            ->view('cooperation.mail.user.password-reset-request');
    }
}
