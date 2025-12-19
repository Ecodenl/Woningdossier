<?php

namespace App\Models;

use App\Traits\Models\HasTranslations;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PriceIndexing
 *
 * @property int $id
 * @property string $short
 * @property array<array-key, mixed> $name
 * @property numeric $percentage
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $translations
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PriceIndexing newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PriceIndexing newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PriceIndexing query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PriceIndexing whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PriceIndexing whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PriceIndexing whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PriceIndexing whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PriceIndexing whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PriceIndexing whereLocales(string $column, array $locales)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PriceIndexing whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PriceIndexing wherePercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PriceIndexing whereShort($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PriceIndexing whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PriceIndexing extends Model
{
    use HasTranslations;

    protected $translatable = [
        'name',
    ];
}
