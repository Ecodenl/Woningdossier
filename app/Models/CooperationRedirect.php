<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CooperationRedirect extends Model
{

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cooperation()
    {
        return $this->belongsTo(Cooperation::class);
    }

    public function scopeFrom(Builder $query, string $slug)
    {
        $slug = strtolower(strip_tags($slug));

        return $query->whereRaw('lower(from_slug) = ?', [$slug]);
    }
}
