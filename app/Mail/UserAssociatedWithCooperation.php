<?php

namespace App\Mail;

use App\Helpers\Queue;
use App\Models\Cooperation;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserAssociatedWithCooperation extends Mailable implements ShouldQueue
{
    use SerializesModels, Queueable;

    public $cooperation;
    public $associatedUser;

    /**
     * Create new message instance.
     *
     * UserCreatedEmail constructor.
     */
    public function __construct(Cooperation $cooperation, User $user)
    {
        $this->onQueue(Queue::APP_HIGH);
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
            ->subject(strip_tags(__('cooperation/mail/account-associated-with-cooperation.subject', ['cooperation_name' => $this->cooperation->name])))
            ->view('cooperation.mail.user.associated.view')
            ->text('cooperation.mail.user.associated.text')
            ->with('userCooperation', $this->cooperation)
            ->with('associatedUser', $this->associatedUser)
            ->with('cooperations', $this->associatedUser->account->cooperations());
    }
}
