<?php

namespace App\Models;

use App\Helpers\TranslatableTrait;
use App\Traits\GetValueTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BuildingCategory.
 *
 * @property int $id
 * @property string $type
 * @property string $name
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\BuildingFeature[] $buildingFeatures
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingCategory translated($attribute, $name, $locale = 'nl')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingCategory whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingCategory whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BuildingCategory extends Model
{
    use TranslatableTrait, GetValueTrait;

    public function buildingFeatures()
    {
        return $this->hasMany(BuildingFeature::class);
    }
}
