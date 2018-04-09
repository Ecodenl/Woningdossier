<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BuildingService
 *
 * @property int $id
 * @property int|null $building_id
 * @property int|null $measure_id
 * @property int|null $service_type_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Appliance[] $appliances
 * @property-read \App\Models\Building|null $building
 * @property-read \App\Models\Measure|null $measure
 * @property-read \App\Models\ServiceType|null $serviceType
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\BuildingServiceValue[] $values
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingService whereBuildingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingService whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingService whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingService whereMeasureId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingService whereServiceTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingService whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BuildingService extends Model
{
	public function building(){
		return $this->belongsTo(Building::class);
	}

	public function serviceType(){
		return $this->belongsTo(ServiceType::class);
	}

}