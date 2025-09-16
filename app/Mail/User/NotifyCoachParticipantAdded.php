<?php

namespace App\Mail\User;

use App\Mail\Middleware\Whitelist;
use App\Models\Cooperation;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;

class NotifyCoachParticipantAdded extends Mailable implements ShouldQueue
{
    use Queueable;

    public User $user;
    public User $coach;
    public Cooperation $userCooperation;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, User $coach)
    {
        $this->user = $user;
        $this->coach = $coach;
        // Both users should have the same cooperation so it shouldn't matter from which user we pluck the cooperation
        $this->userCooperation = $user->cooperation;
    }

    /**
     * Build the message.
     */
    public function build(): static
    {
        return $this
            ->subject(strip_tags(__('cooperation/mail/user/notify-coach-participant-added.subject', ['name' => $this->user->getFullName()])))
            ->view('cooperation.mail.user.notify-coach-participant-added.view')
            ->text('cooperation.mail.user.notify-coach-participant-added.text');
    }

    public function middleware(): array
    {
        return [
            new Whitelist(),
        ];
    }
}
