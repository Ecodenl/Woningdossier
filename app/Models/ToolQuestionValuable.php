<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * App\Models\ToolQuestionValuable
 *
 * @property int $id
 * @property int $tool_question_id
 * @property bool $show
 * @property int $order
 * @property int $tool_question_valuable_id
 * @property string $tool_question_valuable_type
 * @property array<array-key, mixed>|null $extra
 * @property array<array-key, mixed>|null $conditions
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ToolQuestion $toolQuestion
 * @property-read Model|\Eloquent $toolQuestionValuables
 * @method static Builder<static>|ToolQuestionValuable newModelQuery()
 * @method static Builder<static>|ToolQuestionValuable newQuery()
 * @method static Builder<static>|ToolQuestionValuable ordered()
 * @method static Builder<static>|ToolQuestionValuable query()
 * @method static Builder<static>|ToolQuestionValuable visible()
 * @method static Builder<static>|ToolQuestionValuable whereConditions($value)
 * @method static Builder<static>|ToolQuestionValuable whereCreatedAt($value)
 * @method static Builder<static>|ToolQuestionValuable whereExtra($value)
 * @method static Builder<static>|ToolQuestionValuable whereId($value)
 * @method static Builder<static>|ToolQuestionValuable whereOrder($value)
 * @method static Builder<static>|ToolQuestionValuable whereShow($value)
 * @method static Builder<static>|ToolQuestionValuable whereToolQuestionId($value)
 * @method static Builder<static>|ToolQuestionValuable whereToolQuestionValuableId($value)
 * @method static Builder<static>|ToolQuestionValuable whereToolQuestionValuableType($value)
 * @method static Builder<static>|ToolQuestionValuable whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ToolQuestionValuable extends Model
{
    protected $fillable = [
        'show',
        'order',
        'tool_question_id',
        'tool_question_valuable_type',
        'tool_question_valuable_id',
        'extra',
    ];

    protected function casts(): array
    {
        return [
            'show' => 'boolean',
            'extra' => 'array',
            'conditions' => 'array',
        ];
    }

    # Scopes
    #[Scope]
    protected function visible(Builder $query): Builder
    {
        return $query->where('show', true);
    }

    #[Scope]
    protected function ordered(Builder $query): Builder
    {
        return $query->orderBy('order');
    }

    # Relations
    public function toolQuestion(): BelongsTo
    {
        return $this->belongsTo(ToolQuestion::class);
    }

    /**
     * Method retrieves the morphed models.
     */
    public function toolQuestionValuables(): MorphTo
    {
        return $this->morphTo('tool_question_valuable');
    }
}
