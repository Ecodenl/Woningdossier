<?php

namespace App\Models;

use App\Traits\Models\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\BuildingType
 *
 * @property int $id
 * @property int|null $building_type_category_id
 * @property array $name
 * @property int $calculate_value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BuildingFeature> $buildingFeatures
 * @property-read int|null $building_features_count
 * @property-read \App\Models\BuildingTypeCategory|null $buildingTypeCategory
 * @property-read array $translations
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingType query()
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingType whereBuildingTypeCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingType whereCalculateValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BuildingType extends Model
{
    use HasTranslations;

    protected $translatable = [
        'name',
    ];

    public function buildingFeatures()
    {
        return $this->hasMany(BuildingFeature::class);
    }

    public function buildingTypeCategory(): BelongsTo
    {
        return $this->belongsTo(BuildingTypeCategory::class);
    }
}
