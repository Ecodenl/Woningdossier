<?php

namespace App\Models;

use App\Traits\Models\HasTranslations;
use Illuminate\Database\Eloquent\Model;

class SubStep extends Model
{
    use HasTranslations;

    protected $fillable = [
        'name',
        'order',
        'step_id',
        'conditions',
        'sub_step_template_id'
    ];
    protected $translatable = [
        'name',
    ];

    protected $casts = [
        'conditions' => 'array',
    ];

    public function toolQuestions()
    {
        return $this->belongsToMany(ToolQuestion::class, 'sub_step_tool_questions')->withPivot('order');
    }
}
