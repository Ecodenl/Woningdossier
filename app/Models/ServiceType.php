<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceType extends Model
{
    public function measures(){
    	return $this->hasMany(Measure::class);
    }

    public function buildingElements(){
    	return $this->hasMany(BuildingElement::class);
    }

	public function buildingServices(){
		return $this->hasMany(BuildingService::class);
	}
}
