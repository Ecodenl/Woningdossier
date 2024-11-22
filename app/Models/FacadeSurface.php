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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FacadeSurface newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FacadeSurface newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FacadeSurface query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FacadeSurface whereCalculateValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FacadeSurface whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FacadeSurface whereExecutionTermName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FacadeSurface whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FacadeSurface whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FacadeSurface whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FacadeSurface whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FacadeSurface whereLocales(string $column, array $locales)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FacadeSurface whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FacadeSurface whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FacadeSurface whereTermYears($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FacadeSurface whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class FacadeSurface extends Model
{
    use HasTranslations;

    protected $translatable = [
        'name', 'execution_term_name',
    ];
}
