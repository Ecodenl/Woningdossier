<?php

namespace App\Events;

use App\Models\Building;
use App\Models\Cooperation;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ParticipantAddedEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public $addedParticipant;
    public $building;
    public $authenticatable;
    public $cooperation;

    /**
     * Event to be triggered when a participant gets added to a group message / building.
     */
    public function __construct(User $addedParticipant, Building $building, Authenticatable $authenticatable, Cooperation $cooperation)
    {
        $this->addedParticipant = $addedParticipant;
        $this->building = $building;
        $this->authenticatable = $authenticatable;
        $this->cooperation = $cooperation;
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
