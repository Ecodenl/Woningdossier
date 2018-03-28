<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Building
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $type_id
 * @property string $street
 * @property string $number
 * @property string $extension
 * @property string $city
 * @property string $postal_code
 * @property int|null $owner
 * @property int $primary
 * @property string $bag_addressid
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\BuildingElement[] $buildingElements
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\BuildingFeature[] $buildingFeatures
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\BuildingService[] $buildingServices
 * @property-read \App\Models\User $user
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\BuildingUserUsage[] $userUsage
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Building whereBagAddressid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Building whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Building whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Building whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Building whereExtension($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Building whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Building whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Building whereOwner($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Building wherePostalCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Building wherePrimary($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Building whereStreet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Building whereTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Building whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Building whereUserId($value)
 * @mixin \Eloquent
 */
class Building extends Model
{

	public $fillable = [
		'street', 'number', 'city', 'postal_code', 'bag_addressid',
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
		return $this->hasMany(BuildingUserUsage::class);
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\HasOne
	 */
	public function buildingFeatures(){
		return $this->hasOne(BuildingFeature::class);
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

	/**
	 * @return BuildingType|null
	 */
	public function getBuildingType(){
		if ($this->buildingFeatures instanceof BuildingFeature){
			return $this->buildingFeatures->buildingType;
		}
		return null;
	}
}