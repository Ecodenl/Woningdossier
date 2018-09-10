<?php

namespace App\Models;

use App\Helpers\Calculator;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UserActionPlanAdvice.
 *
 * @property int $id
 * @property int $user_id
 * @property int $measure_application_id
 * @property float|null $costs
 * @property float|null $savings_gas
 * @property float|null $savings_electricity
 * @property float|null $savings_money
 * @property int|null $year
 * @property bool $planned
 * @property int|null $planned_year
 * @property int $step_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \App\Models\MeasureApplication $measureApplication
 * @property \App\Models\Step $step
 * @property \App\Models\User $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserActionPlanAdvice forMe()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserActionPlanAdvice forStep(\App\Models\Step $step)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserActionPlanAdvice whereCosts($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserActionPlanAdvice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserActionPlanAdvice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserActionPlanAdvice whereMeasureApplicationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserActionPlanAdvice wherePlanned($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserActionPlanAdvice wherePlannedYear($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserActionPlanAdvice whereSavingsElectricity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserActionPlanAdvice whereSavingsGas($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserActionPlanAdvice whereSavingsMoney($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserActionPlanAdvice whereStepId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserActionPlanAdvice whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserActionPlanAdvice whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserActionPlanAdvice whereYear($value)
 * @mixin \Eloquent
 */
class UserActionPlanAdvice extends Model
{
    public $fillable = [
        'user_id', 'measure_application_id', // old
        'costs', 'savings_gas', 'savings_electricity', 'savings_money',
        'year', 'planned', 'planned_year',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'planned' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function measureApplication()
    {
        return $this->belongsTo(MeasureApplication::class);
    }

    public function step()
    {
        return $this->belongsTo(Step::class);
    }

    public static function getCategorizedActionPlan(User $user)
    {
        $result = [];
        $advices = self::where('user_id', $user->id)
                       ->orderBy('step_id', 'asc')
                       ->orderBy('year', 'asc')
                       ->get();
        /** @var UserActionPlanAdvice $advice */
        foreach ($advices as $advice) {
            /** @var MeasureApplication $measureApplication */
            $measureApplication = $advice->measureApplication;

            if (is_null($advice->year)) {
                $advice->year = $advice->getAdviceYear();
                // re-index costs
                $advice->costs = Calculator::reindexCosts($advice->costs, null, $advice->year);
            }

            if (! array_key_exists($measureApplication->measure_type, $result)) {
                $result[$measureApplication->measure_type] = [];
            }
            if (! array_key_exists($advice->step->name, $result[$measureApplication->measure_type])) {
                $result[$measureApplication->measure_type][$advice->step->name] = [];
            }

            $result[$measureApplication->measure_type][$advice->step->name][] = $advice;
        }

        return $result;
    }

    public function getAdviceYear()
    {
        // todo Find a neater solution for this as this was one of many additions in hindsight
        // Step slug => element short
        $slugElements = [
            'wall-insulation' => 'wall-insulation',
            //'insulated-glazing' => 'living-rooms-windows', // this is nonsense.. there's no location specification in this step, while there is on general-data
            'floor-insulation' => 'floor-insulation',
            //'roof-insulation' => 'roof-insulation',
        ];
        if (! $this->step instanceof Step) {
            return null;
        }

        if ('insulated-glazing' == $this->step->slug) {
            $userInterest = $this->user->getInterestedType('measure_application', $this->measureApplication->id);
            if (! $userInterest instanceof UserInterest) {
                return null;
            }
            if (1 == $userInterest->interest->calculate_value) {
                return Carbon::now()->year;
            }
            if (2 == $userInterest->interest->calculate_value) {
                return Carbon::now()->year + 5;
            }

            return null;
        }

        if (! array_key_exists($this->step->slug, $slugElements)) {
            return null;
        }
        $elementShort = $slugElements[$this->step->slug];
        $element = Element::where('short', $elementShort)->first();
        if (! $element instanceof Element) {
            return null;
        }
        $userInterest = $this->user->getInterestedType('element', $element->id);
        if (! $userInterest instanceof UserInterest) {
            return null;
        }
        if (1 == $userInterest->interest->calculate_value) {
            return Carbon::now()->year;
        }
        if (2 == $userInterest->interest->calculate_value) {
            return Carbon::now()->year + 5;
        }

        return null;
    }

    /**
     * Scope a query to only include the current user.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForMe($query)
    {
        return $query->where('user_id', \Auth::id());
    }

    /**
     * Scope a query to only include results for the particular step.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param Step                                  $step
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForStep($query, Step $step)
    {
        return $query->where('step_id', $step->id);
    }
}
