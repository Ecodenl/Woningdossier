<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PvPanelYield
 *
 * @property int $id
 * @property int $angle
 * @property int $pv_panel_orientation_id
 * @property float $yield
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\PvPanelOrientation $orientation
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PvPanelYield newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PvPanelYield newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PvPanelYield query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PvPanelYield whereAngle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PvPanelYield whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PvPanelYield whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PvPanelYield wherePvPanelOrientationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PvPanelYield whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PvPanelYield whereYield($value)
 * @mixin \Eloquent
 */
class PvPanelYield extends Model
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function orientation()
    {
        return $this->belongsTo(PvPanelOrientation::class);
    }
}
