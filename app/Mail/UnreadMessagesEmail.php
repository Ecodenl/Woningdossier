<?php

namespace App\Mail;

use App\Models\Cooperation;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UnreadMessagesEmail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public $user;
    public $userCooperation;
    public $unreadMessageCount;

    /**
     * UnreadMessagesEmail constructor.
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
        return $this->view('cooperation.mail.user.unread-message-count.view')
                    ->text('cooperation.mail.user.unread-message-count.text')
                    ->subject(strip_tags(trans_choice('cooperation/mail/unread-message-count.subject', $this->unreadMessageCount, [
                        'unread_message_count' => $this->unreadMessageCount,
                    ])));
    }
}
