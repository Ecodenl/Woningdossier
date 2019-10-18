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

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function (Step $step) {
            foreach (Cooperation::all() as $cooperation) {
                $cooperationStepsQuery = $cooperation->steps();
                $cooperationStepsQuery->attach($step->id);
                $cooperationStep = $cooperationStepsQuery->find($step->id);
                $cooperationStepsQuery->updateExistingPivot($cooperationStep->id, ['order' => $step->order]);
            }
        });
        // for now, we keep it in kees.
//        static::addGlobalScope(new CooperationScope());
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
