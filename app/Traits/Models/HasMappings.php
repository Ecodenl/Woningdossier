<?php

namespace App\Traits\Models;

use App\Services\MappingService;
use Illuminate\Database\Eloquent\Model;
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

            MappingService::init()->from($model)->detach();
        });
    }
}
