<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BuildingInsulatedGlazing extends Model
{
	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
    public function measureApplication(){
    	return $this->belongsTo(MeasureApplication::class);
    }

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
    public function insulatedGlazing(){
    	return $this->belongsTo(InsulatingGlazing::class);
    }

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
    public function buildingHeating(){
    	return $this->belongsTo(BuildingHeating::class);
    }
}
