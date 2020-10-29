<?php

namespace App\Events;

use App\Models\Building;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FillingToolForUserEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public $building;
    public $userThatIsFillingTool;

    /**
     * FillingToolForUserEvent constructor.
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
