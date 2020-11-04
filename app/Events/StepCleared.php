<?php

namespace App\Events;

use App\Models\InputSource;
use App\Models\Step;
use App\Models\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class StepCleared
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

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
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
