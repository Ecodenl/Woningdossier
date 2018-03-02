<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\RoofType
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\BuildingFeature[] $buildingFeatures
 * @mixin \Eloquent
 * @property int $id
 * @property string $name
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RoofType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RoofType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RoofType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RoofType whereUpdatedAt($value)
 */
class RoofType extends Model
{
    public function buildingFeatures(){
    	return $this->hasMany(BuildingFeature::class);
    }
}