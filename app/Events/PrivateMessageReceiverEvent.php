<?php

namespace App\Events;

use App\Models\Account;
use App\Models\Cooperation;
use App\Models\PrivateMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PrivateMessageReceiverEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public $privateMessage;
    public $cooperation;
    public $authenticatable;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(PrivateMessage $privateMessage, Cooperation $cooperation, Authenticatable $authenticatable)
    {
        $this->privateMessage = $privateMessage;
        $this->cooperation = $cooperation;
        $this->authenticatable = $authenticatable;
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
