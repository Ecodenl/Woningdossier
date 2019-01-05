<?php

namespace App\Mail;

use App\Models\Cooperation;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserCreatedEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $cooperation;
    public $createdUser;
    public $token;

    /**
     * Create new message instance.
     *
     * UserCreatedEmail constructor.
     *
     * @param Cooperation $cooperation
     */
    public function __construct(Cooperation $cooperation, User $createdUser, $token)
    {
        $this->token = $token;
        $this->cooperation = $cooperation;
        $this->createdUser = $createdUser;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // we cant send $cooperation, will be overwritten
        return $this->view('cooperation.mail.user.created')
            ->with('userCooperation', $this->cooperation)
            ->with('createdUser', $this->createdUser)
            ->with('token', $this->token);
    }
}
