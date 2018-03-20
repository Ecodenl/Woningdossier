<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BuildingType
 *
 * @property int $id
 * @property string $name
 * @property int $calculate_value
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\BuildingFeature[] $buildingFeatures
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingType whereCalculateValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BuildingType extends Model
{
    //
	public function buildingFeatures(){
		return $this->hasMany(BuildingFeature::class);
	}


}
