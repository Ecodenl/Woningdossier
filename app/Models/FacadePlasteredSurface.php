<?php

namespace App\Models;

use App\Traits\Models\HasTranslations;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\FacadePlasteredSurface
 *
 * @property int $id
 * @property array $name
 * @property int|null $calculate_value
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $translations
 * @method static \Illuminate\Database\Eloquent\Builder|FacadePlasteredSurface newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FacadePlasteredSurface newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FacadePlasteredSurface query()
 * @method static \Illuminate\Database\Eloquent\Builder|FacadePlasteredSurface whereCalculateValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FacadePlasteredSurface whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FacadePlasteredSurface whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FacadePlasteredSurface whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder|FacadePlasteredSurface whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder|FacadePlasteredSurface whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder|FacadePlasteredSurface whereLocales(string $column, array $locales)
 * @method static \Illuminate\Database\Eloquent\Builder|FacadePlasteredSurface whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FacadePlasteredSurface whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FacadePlasteredSurface whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class FacadePlasteredSurface extends Model
{
    use HasTranslations;

    protected $translatable = [
        'name',
    ];
}
