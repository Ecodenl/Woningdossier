<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\CooperationRedirect
 *
 * @property int $id
 * @property string $from_slug
 * @property int $cooperation_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Cooperation $cooperation
 * @method static Builder<static>|CooperationRedirect from(string $slug)
 * @method static Builder<static>|CooperationRedirect newModelQuery()
 * @method static Builder<static>|CooperationRedirect newQuery()
 * @method static Builder<static>|CooperationRedirect query()
 * @method static Builder<static>|CooperationRedirect whereCooperationId($value)
 * @method static Builder<static>|CooperationRedirect whereCreatedAt($value)
 * @method static Builder<static>|CooperationRedirect whereFromSlug($value)
 * @method static Builder<static>|CooperationRedirect whereId($value)
 * @method static Builder<static>|CooperationRedirect whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CooperationRedirect extends Model
{

    public function cooperation(): BelongsTo
    {
        return $this->belongsTo(Cooperation::class);
    }

    public function scopeFrom(Builder $query, string $slug)
    {
        $slug = strtolower(strip_tags($slug));

        return $query->whereRaw('lower(from_slug) = ?', [$slug]);
    }
}
