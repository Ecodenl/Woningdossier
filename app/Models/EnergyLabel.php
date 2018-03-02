<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\EnergyLabel
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\BuildingFeature[] $buildingFeatures
 * @mixin \Eloquent
 * @property int $id
 * @property string $name
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EnergyLabel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EnergyLabel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EnergyLabel whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EnergyLabel whereUpdatedAt($value)
 */
class EnergyLabel extends Model
{
    public function buildingFeatures(){
    	return $this->hasMany(BuildingFeature::class);
    }
}
