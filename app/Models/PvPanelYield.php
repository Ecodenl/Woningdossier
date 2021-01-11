<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PvPanelYield.
 *
 * @property int                             $id
 * @property int                             $angle
 * @property int                             $pv_panel_orientation_id
 * @property string                          $yield
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \App\Models\PvPanelOrientation  $orientation
 *
 * @method static \Illuminate\Database\Eloquent\Builder|PvPanelYield newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PvPanelYield newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PvPanelYield query()
 * @method static \Illuminate\Database\Eloquent\Builder|PvPanelYield whereAngle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PvPanelYield whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PvPanelYield whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PvPanelYield wherePvPanelOrientationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PvPanelYield whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PvPanelYield whereYield($value)
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
