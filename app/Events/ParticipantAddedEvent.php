<?php

namespace App\Events;

use App\Models\Building;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ParticipantAddedEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public $addedParticipant;
    public $building;

    /**
     * Event to be triggered when a participant gets added to a group message / building.
     * @param User $addedParticipant
     * @param Building $building
     */
    public function __construct(User $addedParticipant, Building $building)
    {
        $this->building = $building;
        $this->addedParticipant = $addedParticipant;
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
