<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BuildingType
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\BuildingFeature[] $buildingFeatures
 * @mixin \Eloquent
 * @property int $id
 * @property string $name
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingType whereUpdatedAt($value)
 * @property int $calculate_value
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingType whereCalculateValue($value)
 */
class BuildingType extends Model
{
    //
	public function buildingFeatures(){
		return $this->hasMany(BuildingFeature::class);
	}


}
