<?php

namespace App\Traits\Models;

use App\Models\Mapping;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait HasMappings
{
    public function mapping(): MorphOne
    {
        return $this->morphOne(Mapping::class, 'from_model');
    }
}
