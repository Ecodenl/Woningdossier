<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PvPanelYield extends Model
{
    public function orientation(){
    	return $this->belongsTo(PvPanelOrientation::class);
    }
}
