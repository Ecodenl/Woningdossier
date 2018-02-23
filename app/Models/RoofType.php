<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoofType extends Model
{
    public function buildingFeatures(){
    	return $this->hasMany(BuildingFeature::class);
    }
}