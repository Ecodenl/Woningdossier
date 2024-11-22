<?php

namespace App\Models;

use App\Traits\Models\HasTranslations;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BuildingTypeCategory
 *
 * @property int $id
 * @property string $short
 * @property array $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $translations
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingTypeCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingTypeCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingTypeCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingTypeCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingTypeCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingTypeCategory whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingTypeCategory whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingTypeCategory whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingTypeCategory whereLocales(string $column, array $locales)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingTypeCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingTypeCategory whereShort($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingTypeCategory whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BuildingTypeCategory extends Model
{
    use HasTranslations;

    public $translatable = ['name'];
}
