<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BuildingPaintworkStatus extends Model
{

    protected $fillable = ['building_id', 'last_painted_year', 'paintwork_status_id', 'wood_rot_status_id'];
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
