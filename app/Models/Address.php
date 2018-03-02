<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{

	public $fillable = [
		'street', 'number', 'city', 'postal_code',
	];

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function user(){
		return $this->belongsTo(User::class);
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function userUsage(){
		return $this->hasMany(AddressUserUsage::class);
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function buildingFeatures(){
		return $this->hasMany(BuildingFeature::class);
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function buildingElements(){
		return $this->hasMany(BuildingElement::class);
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function buildingServices(){
		return $this->hasMany(BuildingService::class);
	}
}