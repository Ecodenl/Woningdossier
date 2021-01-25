<?php

namespace App\Models;

use App\Helpers\TranslatableTrait;
use App\Traits\HasShortTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Step
 *
 * @property int $id
 * @property int|null $parent_id
 * @property string $slug
 * @property string $short
 * @property string $name
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\MeasureApplication[] $measureApplications
 * @property-read int|null $measure_applications_count
 * @property-read Step|null $parentStep
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Questionnaire[] $questionnaires
 * @property-read int|null $questionnaires_count
 * @property-read \Illuminate\Database\Eloquent\Collection|Step[] $subSteps
 * @property-read int|null $sub_steps_count
 * @method static Builder|Step activeOrderedSteps()
 * @method static Builder|Step newModelQuery()
 * @method static Builder|Step newQuery()
 * @method static Builder|Step onlySubSteps()
 * @method static Builder|Step ordered()
 * @method static Builder|Step query()
 * @method static Builder|Step subStepsForStep(\App\Models\Step $step)
 * @method static Builder|Step translated($attribute, $name, $locale = 'nl')
 * @method static Builder|Step whereCreatedAt($value)
 * @method static Builder|Step whereId($value)
 * @method static Builder|Step whereName($value)
 * @method static Builder|Step whereOrder($value)
 * @method static Builder|Step whereParentId($value)
 * @method static Builder|Step whereShort($value)
 * @method static Builder|Step whereSlug($value)
 * @method static Builder|Step whereUpdatedAt($value)
 * @method static Builder|Step withoutSubSteps()
 * @mixin \Eloquent
 */
class Step extends Model
{
    protected $fillable = ['slug', 'name', 'order'];

    use TranslatableTrait;
    use HasShortTrait;

    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Return the children or so called "sub steps" of a step.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subSteps()
    {
        return $this->hasMany(Step::class, 'parent_id', 'id');
    }

    /**
     * Return the parent of the step.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parentStep()
    {
        return $this->belongsTo(Step::class, 'parent_id', 'id');
    }

    /**
     * Check whether a step has substeps.
     *
     * @return bool
     */
    public function hasSubSteps()
    {
        return $this->subSteps()->exists();
    }

    /**
     * Method to scope the active steps in a ordered order.
     *
     * @return Builder
     */
    public function scopeActiveOrderedSteps(Builder $query)
    {
        return $query->where('steps.short', '!=', 'building-detail')
            ->orderBy('cooperation_steps.order')
            ->where('cooperation_steps.is_active', '1');
    }

    public function scopeSubStepsForStep(Builder $query, Step $step)
    {
        return $query->where('parent_id', $step->id);
    }

    public function scopeOnlySubSteps(Builder $query)
    {
        return $query->whereNotNull('parent_id');
    }

    /**
     * Method to leave out the sub steps.
     *
     * @return Builder
     */
    public function scopeWithoutSubSteps(Builder $query)
    {
        return $query->where('parent_id', null);
    }

    /**
     * Check whether a step is a sub step.
     */
    public function isSubStep(): bool
    {
        // when the parent id is null, its a parent else its a sub step / child.
        return ! is_null($this->parent_id);
    }

    public function questionnaires()
    {
        return $this->hasMany(Questionnaire::class);
    }

    public function hasQuestionnaires()
    {
        if ($this->questionnaires()->count() > 0) {
            return true;
        }

        return false;
    }

    public function scopeOrdered(Builder $query)
    {
        return $query->orderBy('order', 'asc');
    }

    /**
     * Get the measure applications from a step.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function measureApplications()
    {
        return $this->hasMany(MeasureApplication::class);
    }
}
