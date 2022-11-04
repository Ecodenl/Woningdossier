<?php

namespace App\Models;

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
 * @method static Builder|CooperationRedirect from(string $slug)
 * @method static Builder|CooperationRedirect newModelQuery()
 * @method static Builder|CooperationRedirect newQuery()
 * @method static Builder|CooperationRedirect query()
 * @method static Builder|CooperationRedirect whereCooperationId($value)
 * @method static Builder|CooperationRedirect whereCreatedAt($value)
 * @method static Builder|CooperationRedirect whereFromSlug($value)
 * @method static Builder|CooperationRedirect whereId($value)
 * @method static Builder|CooperationRedirect whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
