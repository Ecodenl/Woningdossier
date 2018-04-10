<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BuildingPaintworkStatus extends Model
{
	public function building(){
		return $this->belongsTo(Building::class);
	}

    public function paintworkStatus(){
    	return $this->belongsTo(PaintworkStatus::class);
    }

    public function woodRotStatus(){
    	return $this->belongsTo(WoodRotStatus::class);
    }
}
