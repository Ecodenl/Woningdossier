<?php

namespace App\Events;

use App\Helpers\HoomdossierSession;
use App\Models\Cooperation;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Support\Facades\Log;

class UserChangedHisEmailEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $cooperation;
    public $oldEmail;
    public $newEmail;
    public $user;

    /**
     * UserChangedHisEmailEvent constructor.
     *
     * @param  User  $user
     * @param $oldEmail
     * @param $newEmail
     */
    public function __construct(User $user, $oldEmail, $newEmail)
    {
        Log::debug('User changed his mail from '.$oldEmail.' to '.$newEmail);
        $this->user = $user;
        $this->oldEmail = $oldEmail;
        $this->newEmail = $newEmail;
        $this->cooperation = $user->cooperations()->first();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
