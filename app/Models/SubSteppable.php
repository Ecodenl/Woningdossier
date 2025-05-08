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
 * @property array<array-key, mixed>|null $conditions
 * @property string|null $size
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\SubStep $subStep
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $subSteppable
 * @property-read \App\Models\ToolQuestionType|null $toolQuestionType
 * @method static \Database\Factories\SubSteppableFactory factory($count = null, $state = [])
 * @method static Builder<static>|SubSteppable forScan(\App\Models\Scan $scan)
 * @method static Builder<static>|SubSteppable newModelQuery()
 * @method static Builder<static>|SubSteppable newQuery()
 * @method static Builder<static>|SubSteppable query()
 * @method static Builder<static>|SubSteppable whereConditions($value)
 * @method static Builder<static>|SubSteppable whereCreatedAt($value)
 * @method static Builder<static>|SubSteppable whereId($value)
 * @method static Builder<static>|SubSteppable whereOrder($value)
 * @method static Builder<static>|SubSteppable whereSize($value)
 * @method static Builder<static>|SubSteppable whereSubStepId($value)
 * @method static Builder<static>|SubSteppable whereSubSteppableId($value)
 * @method static Builder<static>|SubSteppable whereSubSteppableType($value)
 * @method static Builder<static>|SubSteppable whereToolQuestionTypeId($value)
 * @method static Builder<static>|SubSteppable whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class SubSteppable extends MorphPivot
{
    use HasFactory;

    protected $table = 'sub_steppables';

    protected function casts(): array
    {
        return [
            'conditions' => 'array',
        ];
    }

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
