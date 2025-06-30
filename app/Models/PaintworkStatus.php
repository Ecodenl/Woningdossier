<?php

namespace App\Models;

use App\Traits\Models\HasTranslations;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PaintworkStatus
 *
 * @property int $id
 * @property array<array-key, mixed> $name
 * @property int|null $calculate_value
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $translations
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaintworkStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaintworkStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaintworkStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaintworkStatus whereCalculateValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaintworkStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaintworkStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaintworkStatus whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaintworkStatus whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaintworkStatus whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaintworkStatus whereLocales(string $column, array $locales)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaintworkStatus whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaintworkStatus whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaintworkStatus whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PaintworkStatus extends Model
{
    use HasTranslations;

    protected $translatable = [
        'name',
    ];
}
