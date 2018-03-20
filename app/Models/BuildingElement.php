<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BuildingElement
 *
 * @property int $id
 * @property int|null $building_id
 * @property int|null $measure_id
 * @property int|null $service_type_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\Building|null $building
 * @property-read \App\Models\Measure|null $measure
 * @property-read \App\Models\ServiceType|null $serviceType
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\BuildingElementValue[] $values
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingElement whereBuildingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingElement whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingElement whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingElement whereMeasureId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingElement whereServiceTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingElement whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BuildingElement extends Model
{
	public function building(){
		return $this->belongsTo(Building::class);
	}

	public function measure(){
		return $this->belongsTo(Measure::class);
	}

	public function serviceType(){
		return $this->belongsTo(ServiceType::class);
	}

	public function values(){
		return $this->hasMany(BuildingElementValue::class);
	}
}