<?php

namespace App\Events;

use App\Models\Cooperation;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * A super admin switched SmartTwin off for this cooperation.
 */
class SmartTwinDisabledForCooperation
{
    use Dispatchable, SerializesModels;

    public function __construct(public Cooperation $cooperation)
    {
    }
}
