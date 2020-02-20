<?php

namespace App\Mail;

use App\Models\Cooperation;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserAssociatedWithCooperation extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public $cooperation;
    public $associatedUser;

    /**
     * Create new message instance.
     *
     * UserCreatedEmail constructor.
     *
     * @param User        $user
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
            ->subject(__('cooperation/mail/account-associated-with-cooperation.subject', ['cooperation_name' => $this->cooperation->name]))
            ->markdown('cooperation.mail.user.associated')
            ->with('userCooperation', $this->cooperation)
            ->with('associatedUser', $this->associatedUser)
            ->with('cooperations', $this->associatedUser->account->cooperations());
    }
}
