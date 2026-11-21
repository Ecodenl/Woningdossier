<?php

namespace App\Events;

use App\Models\Building;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SmartTwinCallbackReceived
{
    use Dispatchable, SerializesModels;

    public Building $building;

    /**
     * The callback entries that were newly added to the building.
     *
     * @var array<int, mixed>
     */
    public array $addedCallbacks;

    /**
     * @param  array<int, mixed>  $addedCallbacks
     */
    public function __construct(Building $building, array $addedCallbacks)
    {
        $this->building       = $building;
        $this->addedCallbacks = $addedCallbacks;
    }
}
