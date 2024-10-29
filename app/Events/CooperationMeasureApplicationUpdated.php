<?php

namespace App\Events;

use App\Models\CooperationMeasureApplication;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CooperationMeasureApplicationUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $measureModel;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(CooperationMeasureApplication $cooperationMeasureApplication)
    {
        $this->measureModel = $cooperationMeasureApplication;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [new PrivateChannel('channel-name')];
    }
}
