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
 * @method static \Illuminate\Database\Eloquent\Builder|InsulatingGlazing newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InsulatingGlazing newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InsulatingGlazing query()
 * @method static \Illuminate\Database\Eloquent\Builder|InsulatingGlazing whereCalculateValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InsulatingGlazing whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InsulatingGlazing whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InsulatingGlazing whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder|InsulatingGlazing whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder|InsulatingGlazing whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder|InsulatingGlazing whereLocales(string $column, array $locales)
 * @method static \Illuminate\Database\Eloquent\Builder|InsulatingGlazing whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InsulatingGlazing whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class InsulatingGlazing extends Model
{
    use HasTranslations;

    protected $translatable = [
        'name',
    ];
}
