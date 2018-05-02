<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BuildingInsulatedGlazing extends Model
{

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'extra' => 'array',
    ];

    protected $fillable = ['building_id', 'measure_application_id', 'insulating_glazing_id', 'building_heating_id', 'm2', 'windows', 'extra'];
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
