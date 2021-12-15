<?php

namespace App\Models;

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

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    public function toolQuestion(): BelongsTo
    {
        return $this->belongsTo(ToolQuestion::class);
    }

    public function toolQuestionCustomValue(): BelongsTo
    {
        return $this->belongsTo(ToolQuestionCustomValue::class);
    }
}
