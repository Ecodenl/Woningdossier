<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeasureProperty extends Model
{
    public function measure(){
    	return $this->belongsTo(Measure::class);
    }
}
