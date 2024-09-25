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
 * @property-read Model|\Eloquent $resolvable
 * @property-read Model|\Eloquent $targetable
 * @method static \Illuminate\Database\Eloquent\Builder|RelatedModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RelatedModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RelatedModel query()
 * @method static \Illuminate\Database\Eloquent\Builder|RelatedModel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RelatedModel whereFromModelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RelatedModel whereFromModelType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RelatedModel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RelatedModel whereTargetModelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RelatedModel whereTargetModelType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RelatedModel whereUpdatedAt($value)
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
