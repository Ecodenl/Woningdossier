<?php

namespace App\Models;

use App\Traits\Models\HasTranslations;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\FacadeDamagedPaintwork
 *
 * @property int $id
 * @property array<array-key, mixed> $name
 * @property int|null $calculate_value
 * @property int $order
 * @property int|null $term_years
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $translations
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FacadeDamagedPaintwork newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FacadeDamagedPaintwork newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FacadeDamagedPaintwork query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FacadeDamagedPaintwork whereCalculateValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FacadeDamagedPaintwork whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FacadeDamagedPaintwork whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FacadeDamagedPaintwork whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FacadeDamagedPaintwork whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FacadeDamagedPaintwork whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FacadeDamagedPaintwork whereLocales(string $column, array $locales)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FacadeDamagedPaintwork whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FacadeDamagedPaintwork whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FacadeDamagedPaintwork whereTermYears($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FacadeDamagedPaintwork whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class FacadeDamagedPaintwork extends Model
{
    use HasTranslations;

    protected $translatable = [
        'name',
    ];
}
