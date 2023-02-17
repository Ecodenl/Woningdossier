<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Mapping
 *
 * @property int $id
 * @property string|null $type
 * @property mixed|null $conditions
 * @property string|null $from_model_type
 * @property int|null $from_model_id
 * @property string|null $from_value
 * @property string|null $target_model_type
 * @property int|null $target_model_id
 * @property string|null $target_value
 * @property array|null $target_data
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Model|\Eloquent $mapable
 * @method static \Database\Factories\MappingFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Mapping newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Mapping newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Mapping query()
 * @method static \Illuminate\Database\Eloquent\Builder|Mapping whereConditions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mapping whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mapping whereFromModelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mapping whereFromModelType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mapping whereFromValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mapping whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mapping whereTargetData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mapping whereTargetModelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mapping whereTargetModelType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mapping whereTargetValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mapping whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mapping whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Mapping extends Model
{
    use HasFactory;

    protected $fillable = [
        'conditions',
        'from_model_type',
        'from_model_id',
        'from_value',

        'target_model_type',
        'target_model_id',
        'target_value',
        'target_data'
    ];

    protected $casts = [
        'target_data' => 'array'
    ];

    public function mapable()
    {
        return $this->morphTo('target_model');
    }
}
