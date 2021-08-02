<?php

namespace App;

use App\Traits\GetMyValuesTrait;
use App\Traits\GetValueTrait;
use Illuminate\Database\Eloquent\Model;

class ToolQuestionAnswer extends Model
{
    use GetValueTrait, GetMyValuesTrait;
    protected $fillable = [
        'building_id', 'input_source_id', 'tool_question_id', 'tool_question_custom_value_id', 'answer',
    ];
}
