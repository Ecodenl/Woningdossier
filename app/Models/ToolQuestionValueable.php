<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ToolQuestionValueable extends Model
{
    protected $table = 'tool_question_valueables';


    /**
     * Method retrieves the morphed models.
     *
     * @return MorphTo
     */
    public function toolQuestionValueables(): MorphTo
    {
        return $this->morphTo('tool_question_valueable');
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
