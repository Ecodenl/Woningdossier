<?php

namespace App\Models;

use App\Traits\GetMyValuesTrait;
use App\Traits\GetValueTrait;
use App\Traits\ToolSettingTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BuildingPvPanel.
 *
 * @property int                                 $id
 * @property int                                 $building_id
 * @property int|null                            $input_source_id
 * @property int|null                            $peak_power
 * @property int                                 $number
 * @property int|null                            $pv_panel_orientation_id
 * @property int|null                            $angle
 * @property \Illuminate\Support\Carbon|null     $created_at
 * @property \Illuminate\Support\Carbon|null     $updated_at
 * @property string|null                         $comment
 * @property \App\Models\Building                $building
 * @property \App\Models\InputSource|null        $inputSource
 * @property \App\Models\PvPanelOrientation|null $orientation
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingPvPanel forInputSource(\App\Models\InputSource $inputSource)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingPvPanel forMe(\App\Models\User $user = null)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingPvPanel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingPvPanel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingPvPanel query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingPvPanel residentInput()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingPvPanel whereAngle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingPvPanel whereBuildingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingPvPanel whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingPvPanel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingPvPanel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingPvPanel whereInputSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingPvPanel whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingPvPanel wherePeakPower($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingPvPanel wherePvPanelOrientationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingPvPanel whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BuildingPvPanel extends Model
{
    use GetValueTrait;
    use GetMyValuesTrait;
    use ToolSettingTrait;

    protected $fillable = [
        'building_id',
        'input_source_id',
        'peak_power',
        'number',
        'pv_panel_orientation_id',
        'angle',
        'comment',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function building()
    {
        return $this->belongsTo(Building::class);
    }

    /**
     * Make sure that null is casted to the integer 0.
     *
     * @param string $value
     *
     * @return void
     */
    public function setNumberAttribute($value)
    {
        if (is_null($value)) {
            $value = 0;
        }

        $this->attributes['number'] = $value;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function orientation()
    {
        return $this->belongsTo(PvPanelOrientation::class, 'pv_panel_orientation_id', 'id');
    }
}
