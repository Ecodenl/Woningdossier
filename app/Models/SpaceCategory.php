<?php

namespace App\Models;

use App\Traits\Models\HasTranslations;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\SpaceCategory
 *
 * @property int $id
 * @property string $type
 * @property array<array-key, mixed> $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $translations
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpaceCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpaceCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpaceCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpaceCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpaceCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpaceCategory whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpaceCategory whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpaceCategory whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpaceCategory whereLocales(string $column, array $locales)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpaceCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpaceCategory whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpaceCategory whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class SpaceCategory extends Model
{
    use HasTranslations;

    protected $translatable = [
        'name',
    ];
}
