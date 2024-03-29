<?php

namespace App\Events;

use App\Models\Building;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserAllowedAccessToHisBuilding
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public User $user;
    public Building $building;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user, Building $building)
    {
        $this->user = $user;
        $this->building = $building;
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
