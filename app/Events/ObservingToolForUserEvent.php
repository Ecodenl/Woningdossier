<?php

namespace App\Events;

use App\Models\Building;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ObservingToolForUserEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public $building;
    public $userThatIsObservingTool;

    /**
     * create new event instantionnn.
     *
     * @param Building $building
     * @param User     $userThatIsObservingTool
     */
    public function __construct(Building $building, User $userThatIsObservingTool)
    {
        $this->building = $building;
        $this->userThatIsObservingTool = $userThatIsObservingTool;
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
