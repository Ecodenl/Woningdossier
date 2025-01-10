<?php

namespace App\Services\Econobis\Payloads;

use App\Traits\FluentCaller;
use App\Traits\Services\HasBuilding;

abstract class EconobisPayload implements MustBuildPayload
{
    use FluentCaller,
        HasBuilding;
}
