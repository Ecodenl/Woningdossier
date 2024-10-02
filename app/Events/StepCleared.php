<?php

namespace App\Events;

use App\Models\InputSource;
use App\Models\Step;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StepCleared
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public $user;
    public $inputSource;
    public $step;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user, InputSource $inputSource, Step $step)
    {
        $this->inputSource = $inputSource;
        $this->user = $user;
        $this->step = $step;
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
