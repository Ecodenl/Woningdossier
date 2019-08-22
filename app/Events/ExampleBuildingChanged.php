<?php

namespace App\Events;

use App\Models\Building;
use App\Models\ExampleBuilding;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ExampleBuildingChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

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
