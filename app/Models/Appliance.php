<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appliance extends Model
{
	public function buildingServices(){
		return $this->belongsToMany(BuildingService::class);
	}

	public function properties(){
		return $this->hasMany(ApplianceProperty::class);
	}
}
