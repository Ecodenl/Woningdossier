<?php

namespace App\Traits\Models;

use Illuminate\Database\Eloquent\Builder;

trait HasOrder
{
    public static function scopeOrdered(Builder $query, string $direction = 'asc'): Builder
    {
        return $query->orderBy('order', $direction);
    }
}