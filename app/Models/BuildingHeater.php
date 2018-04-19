<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BuildingHeater extends Model
{

    protected $fillable = [
        'building_id', 'pv_panel_orientation_id', 'angle',
    ];
	public function building(){
		return $this->belongsTo(Building::class);
	}
}
