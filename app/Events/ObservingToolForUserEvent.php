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

class ObservingToolForUserEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $building;
    public $buildingOwner;
    public $userThatIsFillingTool;

    /**
     * create new event instantionnn.
     *
     * @param Building $building
     * @param User $buildingOwner
     * @param User $userThatIsFillingTool
     */
    public function __construct(Building $building, User $buildingOwner, User $userThatIsFillingTool)
    {
        $this->building = $building;
        $this->buildingOwner = $buildingOwner;
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
