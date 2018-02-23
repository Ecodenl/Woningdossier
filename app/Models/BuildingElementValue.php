<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BuildingElementValue extends Model
{
    public function buildingElement(){
    	return $this->belongsTo(BuildingElement::class);
    }
}
