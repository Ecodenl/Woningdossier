<?php

namespace App\Events;

use App\Models\Building;
use App\Models\InputSource;
use App\Models\Step;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StepDataHasBeenChanged
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * @var Step
     */
    public $step;
    /**
     * @var Building
     */
    public $building;
    /**
     * @var User
     */
    public $user;

    public $inputSource;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Step $step, Building $building, User $user, InputSource $inputSource)
    {
        $this->step = $step;
        $this->building = $building;
        $this->user = $user;
        $this->inputSource = $inputSource;
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
