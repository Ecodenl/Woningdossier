<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ObjectType
 *
 * @property int $id
 * @property string $name
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\BuildingFeature[] $buildingFeatures
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ObjectType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ObjectType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ObjectType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ObjectType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ObjectType extends Model
{
    public function buildingFeatures(){
    	return $this->hasMany(BuildingFeature::class);
    }
}