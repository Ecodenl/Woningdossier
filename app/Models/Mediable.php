<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * 
 *
 * @property int $media_id
 * @property string $mediable_type
 * @property int $mediable_id
 * @property string $tag
 * @property int $order
 * @property-read \App\Models\Media $media
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $mediable
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mediable newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mediable newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mediable query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mediable whereMediaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mediable whereMediableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mediable whereMediableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mediable whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mediable whereTag($value)
 * @mixin \Eloquent
 */
class Mediable extends MorphPivot
{
    protected $table = 'mediables';

    // Relations
    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class);
    }

    public function mediable(): MorphTo
    {
        return $this->morphTo();
    }
}
