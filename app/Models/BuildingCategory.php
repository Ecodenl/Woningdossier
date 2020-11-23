<?php

namespace App\Models;

use App\Helpers\TranslatableTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BuildingCategory
 *
 * @property int $id
 * @property string $type
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\BuildingFeature[] $buildingFeatures
 * @property-read int|null $building_features_count
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingCategory translated($attribute, $name, $locale = 'nl')
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingCategory whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingCategory whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BuildingCategory extends Model
{
    use TranslatableTrait;

    public function buildingFeatures()
    {
        return $this->hasMany(BuildingFeature::class);
    }
}
