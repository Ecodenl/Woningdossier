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
 * @property-read array $translations
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingTypeCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingTypeCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingTypeCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingTypeCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingTypeCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingTypeCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingTypeCategory whereShort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingTypeCategory whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BuildingTypeCategory extends Model
{
    use HasTranslations;

    public $translatable = ['name'];
}
