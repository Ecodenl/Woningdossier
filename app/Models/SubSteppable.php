<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * App\Models\SubSteppable
 *
 * @property-read \App\Models\SubStep $subStep
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $subSteppable
 * @property-read \App\Models\ToolQuestionType|null $toolQuestionType
 * @method static Builder|SubSteppable forScan(\App\Models\Scan $scan)
 * @method static Builder|SubSteppable newModelQuery()
 * @method static Builder|SubSteppable newQuery()
 * @method static Builder|SubSteppable query()
 * @mixin \Eloquent
 */
class SubSteppable extends MorphPivot
{
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
