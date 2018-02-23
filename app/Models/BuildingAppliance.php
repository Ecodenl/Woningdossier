<?php

namespace App\Models;

use App\Models\Address;
use App\Models\Appliance;
use Illuminate\Database\Eloquent\Model;

class BuildingAppliance extends Model
{
    public function address(){
    	return $this->belongsTo(Address::class);
    }

    public function appliance(){
    	return $this->belongsTo(Appliance::class);
    }
}
