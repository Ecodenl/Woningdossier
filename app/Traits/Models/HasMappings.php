<?php

namespace App\Traits\Models;

use App\Models\Mapping;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait HasMappings
{
    public static function bootHasMappings()
    {
        static::deleting(function (Model $model) {
            $model->mapping()->delete();
        });
    }

    public function mapping(): MorphOne
    {
        return $this->morphOne(Mapping::class, 'from_model');
    }
}
