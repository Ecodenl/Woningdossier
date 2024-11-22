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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofTileStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofTileStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofTileStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofTileStatus whereCalculateValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofTileStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofTileStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofTileStatus whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofTileStatus whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofTileStatus whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofTileStatus whereLocales(string $column, array $locales)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofTileStatus whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofTileStatus whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofTileStatus whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class RoofTileStatus extends Model
{
    use HasTranslations;

    protected $translatable = [
        'name',
    ];
}
