<?php

namespace App\Notifications;

use App\Models\Account;
use App\Mail\ResetPasswordRequest;
use App\Models\Cooperation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The password reset token.
     *
     * @var string
     */
    public $token;

    /**
     * The cooperation the user is associated with.
     *
     * @var Cooperation|null
     */
    public $cooperation;

    /**
     * The account that wants a password reset
     *
     * @var Account
     */
    public $account;

    /**
     * Create a notification instance.
     * @param $token
     * @param Account $account
     * @param $cooperation
     */
    public function __construct($token, Account $account, $cooperation)
    {
        $this->account = $account;
        $this->token = $token;
        $this->cooperation = $cooperation;
    }

    /**
     * Get the notification's channels.
     *
     * @param mixed $notifiable
     *
     * @return array|string
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Build the mail representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return Mailable
     */
    public function toMail($notifiable)
    {
        return new ResetPasswordRequest($this->cooperation, $this->account, $this->token);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
        ];
    }
}
