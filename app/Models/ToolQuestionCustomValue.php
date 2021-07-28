<?php

namespace App\Models;

use App\Traits\Models\HasTranslations;
use Illuminate\Database\Eloquent\Model;

class ToolQuestionCustomValue extends Model
{
    use HasTranslations;

    protected $translatable = [
        'name'
    ];

    protected $fillable = [
        'name',
        'short',
        'order',
        'validation',
        'options',
        'value',
        'tool_question_id',
        'show',
        'extra',
    ];

    protected $casts = [
        'show' => 'boolean',
        'extra' => 'array',
    ];
}
