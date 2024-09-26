<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

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
 * @property-read Model|\Eloquent $mappable
 * @property-read Model|\Eloquent $resolvable
 * @method static \Database\Factories\MappingFactory factory($count = null, $state = [])
 * @method static Builder|Mapping forType(string $type)
 * @method static Builder|Mapping newModelQuery()
 * @method static Builder|Mapping newQuery()
 * @method static Builder|Mapping query()
 * @method static Builder|Mapping whereConditions($value)
 * @method static Builder|Mapping whereCreatedAt($value)
 * @method static Builder|Mapping whereFromModelId($value)
 * @method static Builder|Mapping whereFromModelType($value)
 * @method static Builder|Mapping whereFromValue($value)
 * @method static Builder|Mapping whereId($value)
 * @method static Builder|Mapping whereTargetData($value)
 * @method static Builder|Mapping whereTargetModelId($value)
 * @method static Builder|Mapping whereTargetModelType($value)
 * @method static Builder|Mapping whereTargetValue($value)
 * @method static Builder|Mapping whereType($value)
 * @method static Builder|Mapping whereUpdatedAt($value)
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

    # Scopes
    public function scopeForType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    # Relations
    public function resolvable(): MorphTo
    {
        return $this->morphTo('from_model');
    }

    public function mappable(): MorphTo
    {
        return $this->morphTo('target_model');
    }
}
