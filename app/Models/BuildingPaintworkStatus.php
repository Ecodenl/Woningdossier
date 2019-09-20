<?php

namespace App\Models;

use App\Traits\GetMyValuesTrait;
use App\Traits\GetValueTrait;
use App\Traits\ToolSettingTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BuildingPaintworkStatus
 *
 * @property int $id
 * @property int $building_id
 * @property int|null $input_source_id
 * @property int|null $last_painted_year
 * @property int $paintwork_status_id
 * @property int $wood_rot_status_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Building $building
 * @property-read \App\Models\InputSource|null $inputSource
 * @property-read \App\Models\PaintworkStatus $paintworkStatus
 * @property-read \App\Models\WoodRotStatus $woodRotStatus
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingPaintworkStatus forMe()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingPaintworkStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingPaintworkStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingPaintworkStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingPaintworkStatus residentInput()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingPaintworkStatus whereBuildingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingPaintworkStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingPaintworkStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingPaintworkStatus whereInputSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingPaintworkStatus whereLastPaintedYear($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingPaintworkStatus wherePaintworkStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingPaintworkStatus whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingPaintworkStatus whereWoodRotStatusId($value)
 * @mixin \Eloquent
 */
class BuildingPaintworkStatus extends Model
{
    use GetValueTrait, GetMyValuesTrait, ToolSettingTrait;

    protected $fillable = ['building_id', 'input_source_id', 'last_painted_year',  'paintwork_status_id', 'wood_rot_status_id'];

    public function building()
    {
        return $this->belongsTo(Building::class);
    }

    public function paintworkStatus()
    {
        return $this->belongsTo(PaintworkStatus::class);
    }

    public function woodRotStatus()
    {
        return $this->belongsTo(WoodRotStatus::class);
    }
}
