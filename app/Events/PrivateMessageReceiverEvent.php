<?php

namespace App\Events;

use App\Models\PrivateMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class PrivateMessageReceiverEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $privateMessage;
    /**
     * Create a new event instance.
     *
     * @param PrivateMessage $privateMessage
     * @return void
     */
    public function __construct(PrivateMessage $privateMessage)
    {
        $this->privateMessage = $privateMessage;
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
