<?php

namespace App\Services\Models;

use App\Models\CooperationPreset;
use App\Traits\FluentCaller;

class CooperationPresetService
{
    use FluentCaller;

    const COOPERATION_MEASURE_APPLICATIONS = 'cooperation-measure-applications';

    protected CooperationPreset $cooperationPreset;

    public function forPreset(CooperationPreset $cooperationPreset): self
    {
        $this->cooperationPreset = $cooperationPreset;
        return $this;
    }

    public function getContent(): array
    {
        return $this->cooperationPreset->cooperationPresetContents()->pluck('content')->toArray();
    }
}