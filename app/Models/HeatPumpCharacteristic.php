<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class HeatPumpCharacteristic extends Model
{
    const TYPE_HYBRID = 'hybrid';
    const TYPE_FULL = 'full';

    public function scopeForHeatPumpConfigurable(
        Builder $query,
        Model $configurable
    ) {
        // todo
    }
}
