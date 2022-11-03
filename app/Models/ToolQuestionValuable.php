<?php

namespace App\Models;

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
 * @property array|null $extra
 * @property array|null $conditions
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ToolQuestion $toolQuestion
 * @property-read Model|\Eloquent $toolQuestionValuables
 * @method static Builder|ToolQuestionValuable newModelQuery()
 * @method static Builder|ToolQuestionValuable newQuery()
 * @method static Builder|ToolQuestionValuable ordered()
 * @method static Builder|ToolQuestionValuable query()
 * @method static Builder|ToolQuestionValuable visible()
 * @method static Builder|ToolQuestionValuable whereConditions($value)
 * @method static Builder|ToolQuestionValuable whereCreatedAt($value)
 * @method static Builder|ToolQuestionValuable whereExtra($value)
 * @method static Builder|ToolQuestionValuable whereId($value)
 * @method static Builder|ToolQuestionValuable whereOrder($value)
 * @method static Builder|ToolQuestionValuable whereShow($value)
 * @method static Builder|ToolQuestionValuable whereToolQuestionId($value)
 * @method static Builder|ToolQuestionValuable whereToolQuestionValuableId($value)
 * @method static Builder|ToolQuestionValuable whereToolQuestionValuableType($value)
 * @method static Builder|ToolQuestionValuable whereUpdatedAt($value)
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

    protected $casts = [
        'show' => 'boolean',
        'extra' => 'array',
        'conditions' => 'array',
    ];

    # Scopes
    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('show', true);
    }

    public function scopeOrdered(Builder $query): Builder
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
     *
     * @return MorphTo
     */
    public function toolQuestionValuables(): MorphTo
    {
        return $this->morphTo('tool_question_valuable');
    }
}
