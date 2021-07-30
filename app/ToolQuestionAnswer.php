<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ToolQuestionAnswer extends Model
{
    protected $fillable = [
        'building_id', 'input_source_id', 'tool_question_id', 'tool_question_custom_value_id', 'answer',
    ];
}
