<?php

namespace App\Models;

use App\Traits\Models\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'slug',
        'order',
        'step_id',
        'conditions',
        'sub_step_template_id'
    ];
    protected $translatable = [
        'name',
        'slug',
    ];

    protected $casts = [
        'conditions' => 'array',
    ];

    public function step(): BelongsTo
    {
        return $this->belongsTo(Step::class);
    }

    public function getRouteKeyName(): string
    {
        $locale = app()->getLocale();
        return "slug->{$locale}";
    }

    public function subStepTemplate(): BelongsTo
    {
        return $this->belongsTo(SubStepTemplate::class);
    }

    public function toolQuestions()
    {
        return $this->belongsToMany(ToolQuestion::class, 'sub_step_tool_questions')
            ->orderBy('order')
            ->withPivot('order');
    }
}
