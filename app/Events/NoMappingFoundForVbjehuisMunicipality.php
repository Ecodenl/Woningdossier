<?php

namespace App\Events;

use App\Models\Municipality;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NoMappingFoundForVbjehuisMunicipality
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Municipality $municipality;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Municipality $municipality)
    {
        $this->municipality = $municipality;
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
