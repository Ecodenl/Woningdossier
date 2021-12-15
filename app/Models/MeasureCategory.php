<?php

namespace App\Models;

use App\Traits\Models\HasTranslations;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\MeasureCategory
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Measure[] $categories
 * @property-read int|null $categories_count
 * @method static \Illuminate\Database\Eloquent\Builder|MeasureCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MeasureCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MeasureCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|MeasureCategory translated($attribute, $name, $locale = 'nl')
 * @method static \Illuminate\Database\Eloquent\Builder|MeasureCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MeasureCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MeasureCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MeasureCategory whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class MeasureCategory extends Model
{
    use HasTranslations;

    protected $translatable = [
        'name',
    ];

    public function categories()
    {
        return $this->belongsToMany(Measure::class);
    }
}
