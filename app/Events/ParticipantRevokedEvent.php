<?php

namespace App\Events;

use App\Models\Building;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ParticipantRevokedEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public $revokedParticipant;
    public $building;

    /**
     * Event to be triggered when a participant gets revoked from a group message / building.
     *
     * ParticipantRevokedEvent constructor.
     *
     * @param User     $revokedParticipant
     * @param Building $building
     */
    public function __construct(User $revokedParticipant, Building $building)
    {
        $this->revokedParticipant = $revokedParticipant;
        $this->building = $building;
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
