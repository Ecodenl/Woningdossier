<?php

namespace App\Events;

use App\Models\Building;
use App\Models\ExampleBuilding;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ExampleBuildingChanged
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * @var Building
     */
    public $building;
    /**
     * @var ExampleBuilding|null
     */
    public $from;
    /**
     * @var ExampleBuilding
     */
    public $to;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Building $building, $from, ExampleBuilding $to)
    {
        $this->building = $building;
        $this->from = $from;
        $this->to = $to;
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
