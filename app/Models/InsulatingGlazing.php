<?php

namespace App\Models;

use App\Traits\Models\HasTranslations;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\InsulatingGlazing
 *
 * @property int $id
 * @property array $name
 * @property int|null $calculate_value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $translations
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InsulatingGlazing newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InsulatingGlazing newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InsulatingGlazing query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InsulatingGlazing whereCalculateValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InsulatingGlazing whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InsulatingGlazing whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InsulatingGlazing whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InsulatingGlazing whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InsulatingGlazing whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InsulatingGlazing whereLocales(string $column, array $locales)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InsulatingGlazing whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InsulatingGlazing whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class InsulatingGlazing extends Model
{
    use HasTranslations;

    protected $translatable = [
        'name',
    ];
}
