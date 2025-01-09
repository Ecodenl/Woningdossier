<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PvPanelYield
 *
 * @property int $id
 * @property int $angle
 * @property int $pv_panel_orientation_id
 * @property numeric $yield
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\PvPanelOrientation|null $orientation
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PvPanelYield newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PvPanelYield newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PvPanelYield query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PvPanelYield whereAngle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PvPanelYield whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PvPanelYield whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PvPanelYield wherePvPanelOrientationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PvPanelYield whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PvPanelYield whereYield($value)
 * @mixin \Eloquent
 */
class PvPanelYield extends Model
{
    protected $casts = [
        'yield' => 'decimal:2',
    ];

    public function orientation(): BelongsTo
    {
        return $this->belongsTo(PvPanelOrientation::class);
    }
}
