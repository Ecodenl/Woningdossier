<?php

namespace App\Notifications;

use App\Helpers\Queue;
use App\Mail\RequestAccountConfirmationEmail;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;

class VerifyEmailNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var User
     */
    public $user;

    public function __construct(User $user)
    {
        $this->user = $user;
        $this->onQueue(Queue::APP_HIGH);
    }

    /**
     * Get the notification's channels.
     *
     * @param mixed $notifiable
     *
     * @return array|string
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the verification URL for the given notifiable.
     *
     * @param mixed $notifiable
     *
     * @return string
     */
    protected function verificationUrl($notifiable)
    {
        return URL::temporarySignedRoute(
            'cooperation.auth.verification.verify',
            Carbon::now()->addMinutes(config('auth.passwords.users.expire')),
            [
                'cooperation' => $this->user->cooperation,
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }

    /**
     * Build the mail representation of the notification.
     *
     * @param $notifiable
     *
     * @return RequestAccountConfirmationEmail
     */
    public function toMail($notifiable): RequestAccountConfirmationEmail
    {
        $verifyUrl = $this->verificationUrl($notifiable);
        return new RequestAccountConfirmationEmail($this->user, $verifyUrl);
    }
}
