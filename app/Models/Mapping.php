<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * App\Models\Mapping
 *
 * @property int $id
 * @property string|null $type
 * @property string|null $conditions
 * @property string|null $from_model_type
 * @property int|null $from_model_id
 * @property string|null $from_value
 * @property string|null $target_model_type
 * @property int|null $target_model_id
 * @property string|null $target_value
 * @property array<array-key, mixed>|null $target_data
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Model|\Eloquent|null $mappable
 * @property-read Model|\Eloquent|null $resolvable
 * @method static \Database\Factories\MappingFactory factory($count = null, $state = [])
 * @method static Builder<static>|Mapping forType(string $type)
 * @method static Builder<static>|Mapping newModelQuery()
 * @method static Builder<static>|Mapping newQuery()
 * @method static Builder<static>|Mapping query()
 * @method static Builder<static>|Mapping whereConditions($value)
 * @method static Builder<static>|Mapping whereCreatedAt($value)
 * @method static Builder<static>|Mapping whereFromModelId($value)
 * @method static Builder<static>|Mapping whereFromModelType($value)
 * @method static Builder<static>|Mapping whereFromValue($value)
 * @method static Builder<static>|Mapping whereId($value)
 * @method static Builder<static>|Mapping whereTargetData($value)
 * @method static Builder<static>|Mapping whereTargetModelId($value)
 * @method static Builder<static>|Mapping whereTargetModelType($value)
 * @method static Builder<static>|Mapping whereTargetValue($value)
 * @method static Builder<static>|Mapping whereType($value)
 * @method static Builder<static>|Mapping whereUpdatedAt($value)
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

    protected function casts(): array
    {
        return [
            'target_data' => 'array'
        ];
    }

    # Scopes
    #[Scope]
    protected function forType(Builder $query, string $type): Builder
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
