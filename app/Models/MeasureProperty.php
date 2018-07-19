<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\MeasureProperty
 *
 * @property int $id
 * @property int|null $measure_id
 * @property string $name
 * @property string $value
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\Measure|null $measure
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MeasureProperty whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MeasureProperty whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MeasureProperty whereMeasureId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MeasureProperty whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MeasureProperty whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MeasureProperty whereValue($value)
 * @mixin \Eloquent
 */
class MeasureProperty extends Model
{
    public function measure(){
    	return $this->belongsTo(Measure::class);
    }
}
