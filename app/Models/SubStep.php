<?php

namespace App\Models;

use App\Traits\Models\HasTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\SubStep
 *
 * @property int $id
 * @property array $name
 * @property array $slug
 * @property int $order
 * @property array|null $conditions
 * @property int $step_id
 * @property int $sub_step_template_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read array $translations
 * @property-read \App\Models\Step $step
 * @property-read \App\Models\SubStepTemplate $subStepTemplate
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ToolQuestion[] $toolQuestions
 * @property-read int|null $tool_questions_count
 * @method static Builder|SubStep newModelQuery()
 * @method static Builder|SubStep newQuery()
 * @method static Builder|SubStep ordered()
 * @method static Builder|SubStep query()
 * @method static Builder|SubStep whereConditions($value)
 * @method static Builder|SubStep whereCreatedAt($value)
 * @method static Builder|SubStep whereId($value)
 * @method static Builder|SubStep whereName($value)
 * @method static Builder|SubStep whereOrder($value)
 * @method static Builder|SubStep whereSlug($value)
 * @method static Builder|SubStep whereStepId($value)
 * @method static Builder|SubStep whereSubStepTemplateId($value)
 * @method static Builder|SubStep whereUpdatedAt($value)
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

    # Model methods
    public function getRouteKeyName(): string
    {
        $locale = app()->getLocale();
        return "slug->{$locale}";
    }

    public function getRouteKey()
    {
        return $this->slug;
    }

    # Scopes
    public function scopeOrdered(Builder $query)
    {
        return $query->orderBy('order');
    }

    // TODO: Slug trait?
    public function scopeBySlug(Builder $query, string $slug, string $locale = 'nl'): Builder
    {
        return $query->where("slug->{$locale}", $slug);
    }

    public function scopeForScan(Builder $query, Scan $scan): Builder
    {
        return $query->whereHas('step', function ($query) use ($scan) {
            $query->where('scan_id', $scan->id);
        });
    }

    # Relations
    public function step(): BelongsTo
    {
        return $this->belongsTo(Step::class);
    }

    public function subStepTemplate(): BelongsTo
    {
        return $this->belongsTo(SubStepTemplate::class);
    }

    public function toolQuestions()
    {
        return $this->morphedByMany(ToolQuestion::class, 'sub_steppable')
            ->using(SubSteppable::class)
            ->orderBy('order')
            ->withPivot('order', 'size', 'conditions', 'tool_question_type_id');
    }

    public function subSteppables()
    {
        return $this->hasMany(SubSteppable::class);
    }

    /**
     * Get the parent commentable model (post or video).
     */
    public function commentable()
    {
        return $this->morphTo();
    }
}
