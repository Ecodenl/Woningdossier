<?php

namespace App\Models;

use App\Traits\Models\HasTranslations;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ComfortLevelTapWater
 *
 * @property int $id
 * @property array $name
 * @property int $calculate_value
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $translations
 * @method static \Illuminate\Database\Eloquent\Builder|ComfortLevelTapWater newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ComfortLevelTapWater newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ComfortLevelTapWater query()
 * @method static \Illuminate\Database\Eloquent\Builder|ComfortLevelTapWater whereCalculateValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComfortLevelTapWater whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComfortLevelTapWater whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComfortLevelTapWater whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder|ComfortLevelTapWater whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder|ComfortLevelTapWater whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder|ComfortLevelTapWater whereLocales(string $column, array $locales)
 * @method static \Illuminate\Database\Eloquent\Builder|ComfortLevelTapWater whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComfortLevelTapWater whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComfortLevelTapWater whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ComfortLevelTapWater extends Model
{
    use HasTranslations;

    protected $translatable = [
        'name',
    ];
}
