<?php

namespace App\Traits\Models;

use App\Models\Mapping;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasMappings
{
    public static function bootHasMappings()
    {
        static::deleting(function (Model $model) {
            $model->mappings()->delete();
        });
    }

    public function mappings(): MorphMany
    {
        return $this->morphMany(Mapping::class, 'from_model');
    }
}
