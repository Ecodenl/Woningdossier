<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\EnergyLabel
 *
 * @property int $id
 * @property string $name
 * @property int|null $calculate_value
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\BuildingFeature[] $buildingFeatures
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EnergyLabel whereCalculateValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EnergyLabel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EnergyLabel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EnergyLabel whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EnergyLabel whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class EnergyLabel extends Model
{
    public function buildingFeatures(){
    	return $this->hasMany(BuildingFeature::class);
    }
}
