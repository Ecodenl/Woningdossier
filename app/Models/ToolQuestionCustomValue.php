<?php

namespace App\Models;

use App\Helpers\TranslatableTrait;
use Illuminate\Database\Eloquent\Model;

class ToolQuestionCustomValue extends Model
{
    use TranslatableTrait;

    protected $translatable = ['name'];

    protected $fillable = ['name', 'order', 'validation', 'options', 'value', 'tool_question_id', 'show'];

    protected $casts = [
        'show' => 'boolean',
    ];
}
