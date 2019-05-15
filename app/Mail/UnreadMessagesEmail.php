<?php

namespace App\Mail;

use App\Models\Cooperation;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class UnreadMessagesEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $userCooperation;
    public $unreadMessageCount;

    /**
     * UnreadMessagesEmail constructor.
     *
     * @param  User  $user
     * @param Cooperation $cooperation
     * @param  int  $unreadMessageCount
     */
    public function __construct(User $user, Cooperation $cooperation, int $unreadMessageCount)
    {
        $this->user = $user;
        $this->userCooperation = $cooperation;
        $this->unreadMessageCount = $unreadMessageCount;

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('cooperation.mail.user.unread-message-count')
                    ->subject(__('mail.unread-message-count.subject', [
                        'unread_message_count' => $this->unreadMessageCount
                    ]));
    }
}
