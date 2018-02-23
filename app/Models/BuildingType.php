<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BuildingType extends Model
{
    //
	public function buildingFeatures(){
		return $this->hasMany(BuildingFeature::class);
	}


}
