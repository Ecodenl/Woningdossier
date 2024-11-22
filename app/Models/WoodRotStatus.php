<?php

namespace App\Models;

use App\Traits\Models\HasTranslations;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\WoodRotStatus
 *
 * @property int $id
 * @property array $name
 * @property int|null $calculate_value
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $translations
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WoodRotStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WoodRotStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WoodRotStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WoodRotStatus whereCalculateValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WoodRotStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WoodRotStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WoodRotStatus whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WoodRotStatus whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WoodRotStatus whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WoodRotStatus whereLocales(string $column, array $locales)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WoodRotStatus whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WoodRotStatus whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WoodRotStatus whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class WoodRotStatus extends Model
{
    use HasTranslations;

    protected $translatable = [
        'name',
    ];
}
