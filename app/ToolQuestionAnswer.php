<?php

namespace App;

use App\Models\ToolQuestion;
use App\Models\ToolQuestionCustomValue;
use App\Traits\GetMyValuesTrait;
use App\Traits\GetValueTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ToolQuestionAnswer extends Model
{
    use GetValueTrait, GetMyValuesTrait;

    protected $fillable = [
        'building_id', 'input_source_id', 'tool_question_id', 'tool_question_custom_value_id', 'answer',
    ];

    public function toolQuestion(): BelongsTo
    {
        return $this->belongsTo(ToolQuestion::class);
    }

    public function toolQuestionCustomValue(): BelongsTo
    {
        return $this->belongsTo(ToolQuestionCustomValue::class);
    }
}
