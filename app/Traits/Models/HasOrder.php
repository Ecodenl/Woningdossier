<?php

namespace App\Traits\Models;

use Illuminate\Database\Eloquent\Builder;

trait HasOrder {

    public static function scopeOrdered(Builder $query, $direction = 'desc'): Builder
    {
        return $query->orderBy('order', $direction);
    }
}