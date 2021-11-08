<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

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
    ];

    /**
     * Method retrieves the morphed models.
     *
     * @return MorphTo
     */
    public function toolQuestionValuables(): MorphTo
    {
        return $this->morphTo('tool_question_valuable');
    }

    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('show', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('order');
    }

}
