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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FacadePlasteredSurface newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FacadePlasteredSurface newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FacadePlasteredSurface query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FacadePlasteredSurface whereCalculateValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FacadePlasteredSurface whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FacadePlasteredSurface whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FacadePlasteredSurface whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FacadePlasteredSurface whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FacadePlasteredSurface whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FacadePlasteredSurface whereLocales(string $column, array $locales)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FacadePlasteredSurface whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FacadePlasteredSurface whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FacadePlasteredSurface whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class FacadePlasteredSurface extends Model
{
    use HasTranslations;

    protected $translatable = [
        'name',
    ];
}
