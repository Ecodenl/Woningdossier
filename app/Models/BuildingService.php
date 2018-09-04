<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BuildingService
 *
 * @property int $id
 * @property int|null $building_id
 * @property int $service_id
 * @property int|null $service_value_id
 * @property array $extra
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\Building|null $building
 * @property-read \App\Models\Service $service
 * @property-read \App\Models\ServiceType $serviceType
 * @property-read \App\Models\ServiceValue|null $serviceValue
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingService whereBuildingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingService whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingService whereExtra($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingService whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingService whereServiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingService whereServiceValueId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingService whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BuildingService extends Model
{
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'extra' => 'array',
    ];

    protected $fillable = ['service_value_id', 'extra', 'building_id', 'service_id'];

    public function building(){
		return $this->belongsTo(Building::class);
	}

	public function serviceType(){
		return $this->belongsTo(ServiceType::class);
	}

    public function service(){
        return $this->belongsTo(Service::class);
    }

    public function serviceValue(){
        return $this->belongsTo(ServiceValue::class);
    }

}