<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * App\Models\RelatedModel
 *
 * @property int $id
 * @property string|null $from_model_type
 * @property int|null $from_model_id
 * @property string|null $target_model_type
 * @property int|null $target_model_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\TFactory|null $use_factory
 * @property-read Model|\Eloquent|null $resolvable
 * @property-read Model|\Eloquent|null $targetable
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RelatedModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RelatedModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RelatedModel query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RelatedModel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RelatedModel whereFromModelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RelatedModel whereFromModelType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RelatedModel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RelatedModel whereTargetModelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RelatedModel whereTargetModelType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RelatedModel whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class RelatedModel extends Model
{
    use HasFactory;

    protected $fillable = [
        'from_model_type',
        'from_model_id',
        'target_model_type',
        'target_model_id',
    ];

    # Relations
    public function resolvable(): MorphTo
    {
        return $this->morphTo('from_model');
    }

    public function targetable(): MorphTo
    {
        return $this->morphTo('target_model');
    }
}
