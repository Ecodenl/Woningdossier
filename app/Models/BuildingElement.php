<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BuildingElement
 *
 * @property-read \App\Models\Address $address
 * @property-read \App\Models\Measure $measure
 * @property-read \App\Models\ServiceType $serviceType
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\BuildingElementValue[] $values
 * @mixin \Eloquent
 * @property int $id
 * @property int|null $address_id
 * @property int|null $measure_id
 * @property int|null $service_type_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingElement whereAddressId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingElement whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingElement whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingElement whereMeasureId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingElement whereServiceTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingElement whereUpdatedAt($value)
 * @property-read \App\Models\Building $building
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