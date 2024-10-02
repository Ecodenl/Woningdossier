<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use App\Traits\Models\HasTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\App;

/**
 * App\Models\SubStep
 *
 * @property int $id
 * @property array $name
 * @property array $slug
 * @property int $order
 * @property array|null $conditions
 * @property int $step_id
 * @property int|null $sub_step_template_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Model|\Eloquent $commentable
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CompletedSubStep> $completedSubSteps
 * @property-read int|null $completed_sub_steps_count
 * @property-read \App\Models\Step $step
 * @property-read \App\Models\SubStepTemplate|null $subStepTemplate
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SubSteppable> $subSteppables
 * @property-read int|null $sub_steppables_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ToolQuestion> $toolQuestions
 * @property-read int|null $tool_questions_count
 * @property-read mixed $translations
 * @method static Builder|SubStep bySlug(string $slug, string $locale = 'nl')
 * @method static \Database\Factories\SubStepFactory factory($count = null, $state = [])
 * @method static Builder|SubStep forScan(\App\Models\Scan $scan)
 * @method static Builder|SubStep newModelQuery()
 * @method static Builder|SubStep newQuery()
 * @method static Builder|SubStep ordered()
 * @method static Builder|SubStep query()
 * @method static Builder|SubStep whereConditions($value)
 * @method static Builder|SubStep whereCreatedAt($value)
 * @method static Builder|SubStep whereId($value)
 * @method static Builder|SubStep whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static Builder|SubStep whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static Builder|SubStep whereLocale(string $column, string $locale)
 * @method static Builder|SubStep whereLocales(string $column, array $locales)
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
    use HasFactory,
        HasTranslations;

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

    public function getRouteKeyName()
    {
        $locale = App::getLocale();
        return "slug->{$locale}";
    }

    public function getRouteKey()
    {
        return $this->slug;
    }

    // Scopes
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

    // Relations
    public function step(): BelongsTo
    {
        return $this->belongsTo(Step::class);
    }

    public function subStepTemplate(): BelongsTo
    {
        return $this->belongsTo(SubStepTemplate::class);
    }

    public function toolQuestions(): MorphToMany
    {
        return $this->morphedByMany(ToolQuestion::class, 'sub_steppable')
            ->using(SubSteppable::class)
            ->orderBy('order')
            ->withPivot('order', 'size', 'conditions', 'tool_question_type_id');
    }

    public function completedSubSteps(): HasMany
    {
        return $this->hasMany(CompletedSubStep::class);
    }

    public function subSteppables(): HasMany
    {
        return $this->hasMany(SubSteppable::class);
    }

    /**
     * Get the parent commentable model (post or video).
     */
    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }
}
