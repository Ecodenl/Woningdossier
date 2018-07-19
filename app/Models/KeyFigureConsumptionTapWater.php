<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KeyFigureConsumptionTapWater extends Model
{
    public function comfortLevelTapWater(){
    	return $this->belongsTo(ComfortLevelTapWater::class);
    }
}
