<?php

namespace App\Events;

use App\Models\CustomMeasureApplication;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CustomMeasureApplicationChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $measureModel;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(CustomMeasureApplication $customMeasureApplication)
    {
        $this->measureModel = $customMeasureApplication;
    }
    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn(): array
    {
        return new PrivateChannel('channel-name');
    }
}
