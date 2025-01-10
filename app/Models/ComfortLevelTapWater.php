<?php

namespace App\Models;

use App\Traits\Models\HasTranslations;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ComfortLevelTapWater
 *
 * @property int $id
 * @property array<array-key, mixed> $name
 * @property int $calculate_value
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $translations
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ComfortLevelTapWater newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ComfortLevelTapWater newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ComfortLevelTapWater query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ComfortLevelTapWater whereCalculateValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ComfortLevelTapWater whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ComfortLevelTapWater whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ComfortLevelTapWater whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ComfortLevelTapWater whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ComfortLevelTapWater whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ComfortLevelTapWater whereLocales(string $column, array $locales)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ComfortLevelTapWater whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ComfortLevelTapWater whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ComfortLevelTapWater whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ComfortLevelTapWater extends Model
{
    use HasTranslations;

    protected $translatable = [
        'name',
    ];
}
