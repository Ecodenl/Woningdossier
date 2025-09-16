<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\Models\HasTranslations;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BuildingCategory
 *
 * @property int $id
 * @property string $type
 * @property array<array-key, mixed> $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BuildingFeature> $buildingFeatures
 * @property-read int|null $building_features_count
 * @property-read mixed $translations
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingCategory whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingCategory whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingCategory whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingCategory whereLocales(string $column, array $locales)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingCategory whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingCategory whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BuildingCategory extends Model
{
    use HasTranslations;

    protected $translatable = [
        'name',
    ];

    public function buildingFeatures(): HasMany
    {
        return $this->hasMany(BuildingFeature::class);
    }
}
