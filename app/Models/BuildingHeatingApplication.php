<?php

namespace App\Models;

use App\Traits\Models\HasTranslations;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BuildingHeatingApplication
 *
 * @property int $id
 * @property array<array-key, mixed> $name
 * @property string $short
 * @property int $calculate_value
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $translations
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingHeatingApplication newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingHeatingApplication newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingHeatingApplication query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingHeatingApplication whereCalculateValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingHeatingApplication whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingHeatingApplication whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingHeatingApplication whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingHeatingApplication whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingHeatingApplication whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingHeatingApplication whereLocales(string $column, array $locales)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingHeatingApplication whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingHeatingApplication whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingHeatingApplication whereShort($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingHeatingApplication whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BuildingHeatingApplication extends Model
{
    use HasTranslations;

    protected $translatable = [
        'name',
    ];
}
