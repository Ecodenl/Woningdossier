<?php

namespace App\Models;

use App\Helpers\TranslatableTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Step.
 *
 * @property int                                                                       $id
 * @property string                                                                    $slug
 * @property string                                                                    $name
 * @property int                                                                       $order
 * @property \Illuminate\Support\Carbon|null                                           $created_at
 * @property \Illuminate\Support\Carbon|null                                           $updated_at
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\MeasureApplication[] $measureApplications
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\Questionnaire[]      $questionnaires
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Step newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Step newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Step ordered()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Step query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Step translated($attribute, $name, $locale = 'nl')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Step whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Step whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Step whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Step whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Step whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Step whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Step extends Model
{
    protected $fillable = ['slug', 'name', 'order'];

    use TranslatableTrait;


    public function getRouteKeyName()
    {
        return 'slug';
    }
    /**
     * Return the children or so called "sub steps" of a step
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subSteps()
    {
        return $this->hasMany(Step::class, 'parent_id', 'id');
    }

    /**
     * Check whether a step has substeps
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
     * @param Builder $query
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

    /**
     * Method to leave out the sub steps
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeWithoutSubSteps(Builder $query)
    {
        return $query->where('parent_id', null);
    }

    /**
     * Check whether a step is a sub step
     *
     * @return bool
     */
    public function isSubStep(): bool
    {
        // when the parent id is null, its a parent else its a sub step / child.
        return !is_null($this->parent_id);
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
