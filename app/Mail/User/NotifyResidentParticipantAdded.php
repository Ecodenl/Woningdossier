<?php

namespace App\Mail\User;

use App\Models\Cooperation;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;

class NotifyResidentParticipantAdded extends Mailable implements ShouldQueue
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
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->to($this->user->account->email, $this->user->getFullName())
            ->subject(strip_tags(__('cooperation/mail/user/notify-resident-participant-added.subject', ['name' => $this->coach->getFullName()])))
            ->view('cooperation.mail.user.notify-resident-participant-added.view')
            ->text('cooperation.mail.user.notify-resident-participant-added.text');
    }
}