<?php

namespace App\Events;

use App\Models\Cooperation;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * A super admin switched SmartTwin on for this cooperation. Nothing listens yet: it is here so a
 * later need can hook in without touching the switch.
 */
class SmartTwinEnabledForCooperation
{
    use Dispatchable, SerializesModels;

    public function __construct(public Cooperation $cooperation)
    {
    }
}
