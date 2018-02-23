<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BuildingFeature extends Model
{
    public function address(){
    	return $this->belongsTo(Address::class);
    }

    public function objectType(){
    	return $this->belongsTo(ObjectType::class);
    }

    public function buildingCategory(){
    	return $this->belongsTo(BuildingCategory::class);
    }

    public function buildingType(){
    	return $this->belongsTo(BuildingType::class);
    }

    public function roofType(){
    	return $this->belongsTo(RoofType::class);
    }

    public function energyLabel(){
    	return $this->belongsTo(EnergyLabel::class);
    }
}