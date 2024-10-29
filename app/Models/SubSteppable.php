<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * App\Models\SubSteppable
 *
 * @property int $id
 * @property int $order
 * @property int $sub_step_id
 * @property int $sub_steppable_id
 * @property string|null $sub_steppable_type
 * @property int|null $tool_question_type_id
 * @property array|null $conditions
 * @property string|null $size
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\SubStep $subStep
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $subSteppable
 * @property-read \App\Models\ToolQuestionType|null $toolQuestionType
 * @method static \Database\Factories\SubSteppableFactory factory($count = null, $state = [])
 * @method static Builder|SubSteppable forScan(\App\Models\Scan $scan)
 * @method static Builder|SubSteppable newModelQuery()
 * @method static Builder|SubSteppable newQuery()
 * @method static Builder|SubSteppable query()
 * @method static Builder|SubSteppable whereConditions($value)
 * @method static Builder|SubSteppable whereCreatedAt($value)
 * @method static Builder|SubSteppable whereId($value)
 * @method static Builder|SubSteppable whereOrder($value)
 * @method static Builder|SubSteppable whereSize($value)
 * @method static Builder|SubSteppable whereSubStepId($value)
 * @method static Builder|SubSteppable whereSubSteppableId($value)
 * @method static Builder|SubSteppable whereSubSteppableType($value)
 * @method static Builder|SubSteppable whereToolQuestionTypeId($value)
 * @method static Builder|SubSteppable whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class SubSteppable extends MorphPivot
{
    use HasFactory;

    protected $table = 'sub_steppables';

    protected $casts = [
        'conditions' => 'array',
    ];

    # Model methods
    public function isToolQuestion(): bool
    {
        return $this->sub_steppable_type == ToolQuestion::class;
    }

    # Scopes
    public function scopeForScan(Builder $query, Scan $scan): Builder
    {
        return $query->whereHas('subStep', function ($query) use ($scan) {
            $query->forScan($scan);
        });
    }

    # Relations
    public function subStep(): BelongsTo
    {
        return $this->belongsTo(SubStep::class);
    }

    public function subSteppable(): MorphTo
    {
        return $this->morphTo();
    }

    public function toolQuestionType(): BelongsTo
    {
        return $this->belongsTo(ToolQuestionType::class);
    }
}
