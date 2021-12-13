<?php

namespace App\Models;

use App\Traits\HasShortTrait;
use App\Traits\Models\HasTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ToolQuestionCustomValue extends Model
{
    use HasTranslations, HasShortTrait;

    protected $translatable = [
        'name'
    ];

    protected $fillable = [
        'name',
        'short',
        'order',
        'validation',
        'options',
        'tool_question_id',
        'show',
        'extra',
    ];

    protected $casts = [
        'show' => 'boolean',
        'extra' => 'array',
    ];

    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('show', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('order');
    }
}
