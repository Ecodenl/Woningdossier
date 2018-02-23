<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
