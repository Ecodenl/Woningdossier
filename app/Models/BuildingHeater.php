<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BuildingHeater
 *
 * @property int $id
 * @property int $building_id
 * @property int $pv_panel_orientation_id
 * @property int|null $angle
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\Building $building
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingHeater whereAngle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingHeater whereBuildingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingHeater whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingHeater whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingHeater wherePvPanelOrientationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingHeater whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BuildingHeater extends Model
{

    protected $fillable = [
        'building_id', 'pv_panel_orientation_id', 'angle',
    ];
	public function building(){
		return $this->belongsTo(Building::class);
	}
}
