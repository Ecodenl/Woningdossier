<?php

namespace App\Models;

use App\Traits\GetMyValuesTrait;
use App\Traits\ToolSettingTrait;
use App\Traits\GetValueTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BuildingHeater
 *
 * @property int $id
 * @property int $building_id
 * @property int|null $input_source_id
 * @property int $pv_panel_orientation_id
 * @property int|null $angle
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $comment
 * @property-read \App\Models\Building $building
 * @property-read \App\Models\InputSource|null $inputSource
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingHeater forMe()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingHeater newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingHeater newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingHeater query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingHeater whereAngle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingHeater whereBuildingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingHeater whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingHeater whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingHeater whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingHeater whereInputSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingHeater wherePvPanelOrientationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingHeater whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BuildingHeater extends Model
{
    use GetValueTrait, GetMyValuesTrait, ToolSettingTrait;

    protected $fillable = [
        'building_id', 'input_source_id', 'pv_panel_orientation_id', 'angle', 'comment',
    ];

    public function building()
    {
        return $this->belongsTo(Building::class);
    }
}
