<?php

namespace App\Services\Econobis\Payloads;

interface MustBuildPayload
{
    public function buildPayload(): array;
}
