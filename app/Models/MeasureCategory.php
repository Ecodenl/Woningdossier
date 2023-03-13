<?php

namespace App\Models;

use App\Traits\HasShortTrait;
use App\Traits\Models\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * App\Models\MeasureCategory
 *
 * @property int $id
 * @property array $name
 * @property string|null $short
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read array $translations
 * @method static \Illuminate\Database\Eloquent\Builder|MeasureCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MeasureCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MeasureCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|MeasureCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MeasureCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MeasureCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MeasureCategory whereShort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MeasureCategory whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class MeasureCategory extends Model
{
    use HasFactory,
        HasShortTrait,
        HasTranslations;

    protected $fillable = ['name', 'short'];

    protected $translatable = ['name'];

    public static function booted()
    {
        static::saving(function (MeasureCategory $measureCategory) {
            // Upon creation, this isn't yet set
            if (! empty($measureCategory->short)) {
                $measureCategory->clearShortCache($measureCategory->short);
            }
            $measureCategory->short = Str::slug($measureCategory->getTranslation('name', 'nl'));
        });
    }
}
