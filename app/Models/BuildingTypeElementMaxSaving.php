<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BuildingTypeElementMaxSaving extends Model
{
    public function buildingType(){
    	return $this->belongsTo(BuildingType::class);
    }

    public function element(){
    	return $this->belongsTo(Element::class);
    }
}
