<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use PhpParser\Node\Expr\AssignOp\Mod;

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


    public function limitable(): MorphTo
    {
        return $this->morphTo('limiteable');
    }

    /**
     * Simple method to limit the valuables you want to retrieve, pass through a model and limit the valuables that match the model.
     *
     * @param Builder $query
     * @param Model $limitedTo
     * @return Builder
     */
    public function scopeLimitedTo(Builder $query, Model $limitedTo): Builder
    {
        return $query->where('limiteable_id', $limitedTo->id)
            ->where('limiteable_type', get_class($limitedTo))
            ->orWhereNull('limiteable_id');
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
