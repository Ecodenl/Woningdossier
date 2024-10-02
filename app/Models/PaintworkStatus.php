<?php

namespace App\Models;

use App\Traits\Models\HasTranslations;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PaintworkStatus
 *
 * @property int $id
 * @property array $name
 * @property int|null $calculate_value
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $translations
 * @method static \Illuminate\Database\Eloquent\Builder|PaintworkStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PaintworkStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PaintworkStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder|PaintworkStatus whereCalculateValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaintworkStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaintworkStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaintworkStatus whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder|PaintworkStatus whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder|PaintworkStatus whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder|PaintworkStatus whereLocales(string $column, array $locales)
 * @method static \Illuminate\Database\Eloquent\Builder|PaintworkStatus whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaintworkStatus whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaintworkStatus whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PaintworkStatus extends Model
{
    use HasTranslations;

    protected $translatable = [
        'name',
    ];
}
