<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BuildingServiceValue extends Model
{
    public function buildingService(){
    	return $this->belongsTo(BuildingService::class);
    }
}
