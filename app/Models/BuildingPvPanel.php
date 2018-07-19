<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BuildingPvPanel extends Model
{

    protected $fillable = ['building_id', 'peak_power', 'number', 'pv_panel_orientation_id', 'angle'];
	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function building(){
		return $this->belongsTo(Building::class);
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function orientation(){
		return $this->belongsTo(PvPanelOrientation::class);
	}
}
