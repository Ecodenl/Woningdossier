<?php

namespace App\Models;

use App\Traits\GetMyValuesTrait;
use App\Traits\GetValueTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BuildingPaintworkStatus.
 *
 * @property int $id
 * @property int $building_id
 * @property int $last_painted_year
 * @property int $paintwork_status_id
 * @property int $wood_rot_status_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \App\Models\Building $building
 * @property \App\Models\PaintworkStatus $paintworkStatus
 * @property \App\Models\WoodRotStatus $woodRotStatus
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingPaintworkStatus whereBuildingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingPaintworkStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingPaintworkStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingPaintworkStatus whereLastPaintedYear($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingPaintworkStatus wherePaintworkStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingPaintworkStatus whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingPaintworkStatus whereWoodRotStatusId($value)
 * @mixin \Eloquent
 */
class BuildingPaintworkStatus extends Model
{
    use GetValueTrait;
    use GetMyValuesTrait;

    protected $fillable = ['building_id', 'input_source_id', 'last_painted_year', 'paintwork_status_id', 'wood_rot_status_id'];

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
