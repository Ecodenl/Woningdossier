<?php

namespace App\Events;

use App\Models\Building;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class FillingToolForUserEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $building;
    public $userThatIsFillingTool;

    /**
     * FillingToolForUserEvent constructor.
     *
     * @param Building $building
     * @param User $userThatIsFillingTool
     */
    public function __construct(Building $building, User $userThatIsFillingTool)
    {
        $this->building = $building;
        $this->userThatIsFillingTool = $userThatIsFillingTool;
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
