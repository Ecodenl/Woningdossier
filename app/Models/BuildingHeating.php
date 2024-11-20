<?php

namespace App\Models;

use App\Traits\Models\HasTranslations;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BuildingHeating
 *
 * @property int $id
 * @property array $name
 * @property int|null $degree
 * @property int|null $calculate_value
 * @property bool $is_default
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $translations
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingHeating newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingHeating newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingHeating query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingHeating whereCalculateValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingHeating whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingHeating whereDegree($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingHeating whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingHeating whereIsDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingHeating whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingHeating whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingHeating whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingHeating whereLocales(string $column, array $locales)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingHeating whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingHeating whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BuildingHeating extends Model
{
    use HasTranslations;

    protected $translatable = [
        'name',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_default' => 'boolean',
    ];
}
