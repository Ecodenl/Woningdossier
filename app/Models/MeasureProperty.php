<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\MeasureProperty.
 *
 * @property int                             $id
 * @property int|null                        $measure_id
 * @property string                          $name
 * @property string                          $value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \App\Models\Measure|null        $measure
 *
 * @method static \Illuminate\Database\Eloquent\Builder|MeasureProperty newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MeasureProperty newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MeasureProperty query()
 * @method static \Illuminate\Database\Eloquent\Builder|MeasureProperty whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MeasureProperty whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MeasureProperty whereMeasureId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MeasureProperty whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MeasureProperty whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MeasureProperty whereValue($value)
 * @mixin \Eloquent
 */
class MeasureProperty extends Model
{
    public function measure()
    {
        return $this->belongsTo(Measure::class);
    }
}
