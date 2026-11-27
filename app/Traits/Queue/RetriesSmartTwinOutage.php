<?php

namespace App\Traits\Queue;

/**
 * SmartTwin's servers are started on demand, and can take anything from a few seconds to several
 * minutes to come back. The short waits catch the first, the long ones the second, which together
 * keeps a job trying for just under 19 minutes.
 *
 * SmartTwinEventSubscriber::DISPATCH_LOCK_SECONDS has to outlast this.
 */
trait RetriesSmartTwinOutage
{
    public int $tries = 8;

    /** @var array<int, int> */
    public array $backoff = [5, 15, 30, 60, 120, 300, 600];
}
