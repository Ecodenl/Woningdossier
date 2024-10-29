<?php

namespace App\Models;

use App\Traits\Models\HasTranslations;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\SpaceCategory
 *
 * @property int $id
 * @property string $type
 * @property array $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $translations
 * @method static \Illuminate\Database\Eloquent\Builder|SpaceCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SpaceCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SpaceCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|SpaceCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpaceCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpaceCategory whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder|SpaceCategory whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder|SpaceCategory whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder|SpaceCategory whereLocales(string $column, array $locales)
 * @method static \Illuminate\Database\Eloquent\Builder|SpaceCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpaceCategory whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpaceCategory whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class SpaceCategory extends Model
{
    use HasTranslations;

    protected $translatable = [
        'name',
    ];
}
