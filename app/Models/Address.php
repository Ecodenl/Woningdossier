<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    //
	public function user(){
		return $this->belongsTo(User::class);
	}

	public function userUsage(){
		return $this->hasMany(AddressUserUsage::class);
	}

	public function buildingFeatures(){
		return $this->hasMany(BuildingFeature::class);
	}

	public function buildingElements(){
		return $this->hasMany(BuildingElement::class);
	}

	public function buildingServices(){
		return $this->hasMany(BuildingService::class);
	}
}