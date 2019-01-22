<?php

namespace App\Models;

use App\Helpers\TranslatableTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BuildingCategory.
 *
 * @property int $id
 * @property string $type
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\BuildingFeature[] $buildingFeatures
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingCategory query()
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
    use TranslatableTrait;

    public function buildingFeatures()
    {
        return $this->hasMany(BuildingFeature::class);
    }
}
