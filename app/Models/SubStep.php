<?php

namespace App\Models;

use App\Traits\Models\HasTranslations;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\SubStep
 *
 * @method static \Illuminate\Database\Eloquent\Builder|SubStep newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SubStep newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SubStep query()
 * @mixin \Eloquent
 */
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

    public function next(): SubStep
    {
        return SubStep::where('order', '>', $this->order)->first();
    }

    public function previous(): SubStep
    {
        return SubStep::where('order', '<', $this->order)->orderByDesc('order')->first();
    }
}
