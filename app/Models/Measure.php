<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Measure
 *
 * @property int $id
 * @property string $name
 * @property int|null $service_type_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\BuildingElement $buildingElements
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\BuildingService[] $buildingServices
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\MeasureCategory[] $categories
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Opportunity[] $opportunities
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\MeasureProperty[] $properties
 * @property-read \App\Models\ServiceType|null $serviceType
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Measure whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Measure whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Measure whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Measure whereServiceTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Measure whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Measure extends Model
{
    public function serviceType(){
    	return $this->belongsTo(ServiceType::class);
    }

    public function buildingElements(){
    	return $this->belongsTo(BuildingElement::class);
    }

    public function buildingServices(){
    	return $this->hasMany(BuildingService::class);
    }

    public function categories(){
    	return $this->belongsToMany(MeasureCategory::class);
    }

    public function opportunities(){
    	return $this->hasMany(Opportunity::class);
    }

    public function properties(){
    	return $this->hasMany(MeasureProperty::class);
    }
}
