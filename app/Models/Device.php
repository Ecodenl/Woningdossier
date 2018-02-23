<?php

namespace App\Models;

use App\Models\Address;
use App\Models\Measure;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    public function measure(){
    	return $this->belongsTo(Measure::class);
    }

    public function address(){
    	return $this->belongsTo(Address::class);
    }

    public function deviceType(){
    	return $this->belongsTo(DeviceType::class);
    }
}
