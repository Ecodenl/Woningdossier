<?php

namespace App\Models;

use App\Traits\GetMyValuesTrait;
use App\Traits\GetValueTrait;
use App\Traits\ToolSettingTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BuildingHeater.
 *
 * @property int                                 $id
 * @property int                                 $building_id
 * @property int|null                            $input_source_id
 * @property int|null                            $pv_panel_orientation_id
 * @property int|null                            $angle
 * @property \Illuminate\Support\Carbon|null     $created_at
 * @property \Illuminate\Support\Carbon|null     $updated_at
 * @property \App\Models\Building                $building
 * @property \App\Models\InputSource|null        $inputSource
 * @property \App\Models\PvPanelOrientation|null $orientation
 *
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingHeater allInputSources()
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingHeater forBuilding(\App\Models\Building $building)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingHeater forInputSource(\App\Models\InputSource $inputSource)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingHeater forMe(\App\Models\User $user = null)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingHeater newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingHeater newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingHeater query()
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingHeater residentInput()
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingHeater whereAngle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingHeater whereBuildingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingHeater whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingHeater whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingHeater whereInputSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingHeater wherePvPanelOrientationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingHeater whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BuildingHeater extends Model
{
    use GetValueTrait;
    use GetMyValuesTrait;
    use ToolSettingTrait;

    protected $fillable = [
        'building_id', 'input_source_id', 'pv_panel_orientation_id', 'angle',
    ];

    public function building()
    {
        return $this->belongsTo(Building::class);
    }

    public function orientation()
    {
        return $this->belongsTo(PvPanelOrientation::class, 'pv_panel_orientation_id', 'id');
    }
}
