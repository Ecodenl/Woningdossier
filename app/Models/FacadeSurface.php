<?php

namespace App\Models;

use App\Traits\Models\HasTranslations;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\FacadeSurface
 *
 * @property int $id
 * @property array $name
 * @property int|null $calculate_value
 * @property int $order
 * @property array $execution_term_name
 * @property int|null $term_years
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $translations
 * @method static \Illuminate\Database\Eloquent\Builder|FacadeSurface newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FacadeSurface newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FacadeSurface query()
 * @method static \Illuminate\Database\Eloquent\Builder|FacadeSurface whereCalculateValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FacadeSurface whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FacadeSurface whereExecutionTermName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FacadeSurface whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FacadeSurface whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder|FacadeSurface whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder|FacadeSurface whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder|FacadeSurface whereLocales(string $column, array $locales)
 * @method static \Illuminate\Database\Eloquent\Builder|FacadeSurface whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FacadeSurface whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FacadeSurface whereTermYears($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FacadeSurface whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class FacadeSurface extends Model
{
    use HasTranslations;

    protected $translatable = [
        'name', 'execution_term_name',
    ];
}
