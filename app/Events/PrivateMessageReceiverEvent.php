<?php

namespace App\Events;

use App\Models\PrivateMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PrivateMessageReceiverEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public $privateMessage;

    /**
     * Create a new event instance.
     *
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
//        return new PrivateChannel('channel-name');
    }
}
