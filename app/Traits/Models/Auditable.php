<?php

namespace App\Traits\Models;

use App\Helpers\Arr;
use App\Helpers\HoomdossierSession;

trait Auditable
{
    use \OwenIt\Auditing\Auditable;

    public function transformAudit(array $data): array
    {
        // Ensure we save input source and building ID
        Arr::set($data, 'input_source_id', HoomdossierSession::getInputSource());
        Arr::set($data, 'building_id', HoomdossierSession::getBuilding());

        return $data;
    }
}