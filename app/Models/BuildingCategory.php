<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BuildingCategory extends Model
{
    public function buildingFeatures(){
    	return $this->hasMany(BuildingFeature::class);
    }
}