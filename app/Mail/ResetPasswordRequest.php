<?php

namespace App\Mail;

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
    public $user;
    public $token;

    /**
     * Create new message instance.
     *
     * UserCreatedEmail constructor.
     *
     * @param Cooperation $cooperation
     */
    public function __construct(Cooperation $cooperation, $token)
    {
        $this->token = $token;
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
            ->subject(__('mail.reset_password.subject'))
            ->view('cooperation.mail.user.password-reset-request');
    }
}
