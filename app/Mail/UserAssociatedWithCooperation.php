<?php

namespace App\Mail;

use App\Models\Cooperation;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserAssociatedWithCooperation extends Mailable
{
    use Queueable, SerializesModels;

    public $cooperation;
    public $associatedUser;

    /**
     * Create new message instance.
     *
     * UserCreatedEmail constructor.
     *
     * @param Cooperation $cooperation
     */
    public function __construct(Cooperation $cooperation, User $user)
    {
        $this->cooperation = $cooperation;
        $this->associatedUser = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject(__('mail.account-associated-with-cooperation.subject'))
            ->view('cooperation.mail.user.associated')
            ->with('userCooperation', $this->cooperation)
            ->with('associatedUser', $this->associatedUser);
    }
}
