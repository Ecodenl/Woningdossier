<?php

namespace App\Models;

use App\Helpers\TranslatableTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\MeasureCategory.
 *
 * @property int                                                            $id
 * @property string                                                         $name
 * @property \Illuminate\Support\Carbon|null                                $created_at
 * @property \Illuminate\Support\Carbon|null                                $updated_at
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\Measure[] $categories
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MeasureCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MeasureCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MeasureCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MeasureCategory translated($attribute, $name, $locale = 'nl')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MeasureCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MeasureCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MeasureCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MeasureCategory whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class MeasureCategory extends Model
{
    use TranslatableTrait;

    public function categories()
    {
        return $this->belongsToMany(Measure::class);
    }
}
