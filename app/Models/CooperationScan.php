<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * App\Models\CooperationScan
 *
 * @property int $id
 * @property int $cooperation_id
 * @property int $scan_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Cooperation $cooperation
 * @property-read \App\Models\TFactory|null $use_factory
 * @property-read \App\Models\Scan $scan
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CooperationScan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CooperationScan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CooperationScan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CooperationScan whereCooperationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CooperationScan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CooperationScan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CooperationScan whereScanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CooperationScan whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CooperationScan extends Pivot
{
    use HasFactory;

    public function cooperation(): BelongsTo
    {
        return $this->belongsTo(Cooperation::class);
    }

    public function scan(): BelongsTo
    {
        return $this->belongsTo(Scan::class);
    }
}
