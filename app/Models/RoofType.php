<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\HasShortTrait;
use App\Traits\Models\HasTranslations;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\RoofType
 *
 * @property int $id
 * @property array<array-key, mixed> $name
 * @property string $short
 * @property int $calculate_value
 * @property int|null $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BuildingFeature> $buildingFeatures
 * @property-read int|null $building_features_count
 * @property-read mixed $translations
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofType query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofType whereCalculateValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofType whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofType whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofType whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofType whereLocales(string $column, array $locales)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofType whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofType whereShort($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class RoofType extends Model
{
    use HasShortTrait,
        HasTranslations;

    const array PRIMARY_TO_SECONDARY_MAP = [
        'pitched' => 'pitched',
        'flat' => 'flat',
        'none' => 'none',
        'gabled-roof' => 'pitched',
        'rounded-roof' => 'pitched',
        'straw-roof' => 'pitched',
    ];

    const array SECONDARY_ROOF_TYPE_SHORTS = [
        'pitched', 'flat', 'none',
    ];

    const array MEASURE_MAP = [
        'pitched' => [
            'roof-insulation-flat-current' => 'roof-insulation-pitched-inside',
            'roof-insulation-flat-replace-current' => 'roof-insulation-pitched-replace-tiles',
        ],
        'flat' => [
            'roof-insulation-pitched-inside' => 'roof-insulation-flat-current',
            'roof-insulation-pitched-replace-tiles' => 'roof-insulation-flat-replace-current',
        ],
    ];

    protected $translatable = [
        'name',
    ];

    public function buildingFeatures(): HasMany
    {
        return $this->hasMany(BuildingFeature::class);
    }
}
