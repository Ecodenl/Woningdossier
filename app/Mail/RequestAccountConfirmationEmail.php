<?php

namespace App\Mail;

use App\Models\Cooperation;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RequestAccountConfirmationEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var User
     */
    public $user;

    public $cooperation;

    /**
     * Create a new message instance.
     *
     * @param User $user
     * @param Cooperation $cooperation
     * @return void
     */
    public function __construct(User $user, Cooperation $cooperation)
    {
        $this->user = $user;
        $this->cooperation = $cooperation;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->view('cooperation.mail.user.confirm_account');

    }
}
