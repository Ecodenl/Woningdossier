<?php

namespace App\Models;

use App\Traits\Models\HasTranslations;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\FacadeDamagedPaintwork
 *
 * @property int $id
 * @property array $name
 * @property int|null $calculate_value
 * @property int $order
 * @property int|null $term_years
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $translations
 * @method static \Illuminate\Database\Eloquent\Builder|FacadeDamagedPaintwork newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FacadeDamagedPaintwork newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FacadeDamagedPaintwork query()
 * @method static \Illuminate\Database\Eloquent\Builder|FacadeDamagedPaintwork whereCalculateValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FacadeDamagedPaintwork whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FacadeDamagedPaintwork whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FacadeDamagedPaintwork whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder|FacadeDamagedPaintwork whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder|FacadeDamagedPaintwork whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder|FacadeDamagedPaintwork whereLocales(string $column, array $locales)
 * @method static \Illuminate\Database\Eloquent\Builder|FacadeDamagedPaintwork whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FacadeDamagedPaintwork whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FacadeDamagedPaintwork whereTermYears($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FacadeDamagedPaintwork whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class FacadeDamagedPaintwork extends Model
{
    use HasTranslations;

    protected $translatable = [
        'name',
    ];
}
