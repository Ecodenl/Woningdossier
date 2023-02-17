<?php

namespace App\Traits\Models;

use App\Models\Mapping;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

trait HasMappings
{
    public static function bootHasMappings()
    {
        static::deleting(function (Model $model) {
            // We don't want to clear the mapping when soft deleting
            if (in_array(SoftDeletes::class, class_uses_recursive($model))) {
                if (! $model->forceDeleting) {
                    return;
                }
            }

            $model->mappings()->delete();
        });
    }

    public function mappings(): MorphMany
    {
        return $this->morphMany(Mapping::class, 'from_model');
    }
}
