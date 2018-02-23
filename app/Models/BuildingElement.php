<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BuildingElement extends Model
{
	public function address(){
		return $this->belongsTo(Address::class);
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