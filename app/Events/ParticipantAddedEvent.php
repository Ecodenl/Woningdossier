<?php

namespace App\Events;

use App\Models\Building;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ParticipantAddedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $addedParticipant;
    public $building;

    /**
     * Event to be triggered when a participant gets added to a group message / building
     *
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
