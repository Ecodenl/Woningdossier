<?php

namespace App\Models;

use App\Helpers\TranslatableTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BuildingCategory
 *
 * @property int $id
 * @property string $name
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\BuildingFeature[] $buildingFeatures
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingCategory whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BuildingCategory extends Model
{
	use TranslatableTrait;

    public function buildingFeatures(){
    	return $this->hasMany(BuildingFeature::class);
    }
}