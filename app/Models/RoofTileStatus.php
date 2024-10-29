<?php

namespace App\Models;

use App\Traits\Models\HasTranslations;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\RoofTileStatus
 *
 * @property int $id
 * @property array $name
 * @property int|null $calculate_value
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $translations
 * @method static \Illuminate\Database\Eloquent\Builder|RoofTileStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RoofTileStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RoofTileStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder|RoofTileStatus whereCalculateValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoofTileStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoofTileStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoofTileStatus whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder|RoofTileStatus whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder|RoofTileStatus whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder|RoofTileStatus whereLocales(string $column, array $locales)
 * @method static \Illuminate\Database\Eloquent\Builder|RoofTileStatus whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoofTileStatus whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoofTileStatus whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class RoofTileStatus extends Model
{
    use HasTranslations;

    protected $translatable = [
        'name',
    ];
}
