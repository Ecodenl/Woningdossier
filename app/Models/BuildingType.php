<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
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
 * @property-read mixed $translations
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingType query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingType whereBuildingTypeCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingType whereCalculateValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingType whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingType whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingType whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingType whereLocales(string $column, array $locales)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BuildingType extends Model
{
    use HasTranslations;

    protected $translatable = [
        'name',
    ];

    public function buildingFeatures(): HasMany
    {
        return $this->hasMany(BuildingFeature::class);
    }

    public function buildingTypeCategory(): BelongsTo
    {
        return $this->belongsTo(BuildingTypeCategory::class);
    }
}
