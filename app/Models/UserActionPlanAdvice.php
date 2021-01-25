<?php

namespace App\Models;

use App\Traits\GetMyValuesTrait;
use App\Traits\GetValueTrait;
use App\Traits\ToolSettingTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UserActionPlanAdvice
 *
 * @property-read \App\Models\InputSource $inputSource
 * @property-read \App\Models\MeasureApplication $measureApplication
 * @property-read \App\Models\Step $step
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|UserActionPlanAdvice allInputSources()
 * @method static \Illuminate\Database\Eloquent\Builder|UserActionPlanAdvice forBuilding(\App\Models\Building $building)
 * @method static \Illuminate\Database\Eloquent\Builder|UserActionPlanAdvice forInputSource(\App\Models\InputSource $inputSource)
 * @method static \Illuminate\Database\Eloquent\Builder|UserActionPlanAdvice forMe(\App\Models\User $user = null)
 * @method static \Illuminate\Database\Eloquent\Builder|UserActionPlanAdvice forStep(\App\Models\Step $step)
 * @method static \Illuminate\Database\Eloquent\Builder|UserActionPlanAdvice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserActionPlanAdvice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserActionPlanAdvice query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserActionPlanAdvice residentInput()
 * @mixin \Eloquent
 */
class UserActionPlanAdvice extends Model
{
    use GetValueTrait;
    use GetMyValuesTrait;
    use ToolSettingTrait;

    protected $table = 'user_action_plan_advices';

    public $fillable = [
        'user_id', 'measure_application_id', // old
        'costs', 'savings_gas', 'savings_electricity', 'savings_money',
        'year', 'planned', 'planned_year', 'input_source_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'planned' => 'boolean',
    ];

    /**
     * Scope a query to only include results for the particular step.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForStep($query, Step $step)
    {
        return $query->where('step_id', $step->id);
    }

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

    /**
     * Check whether someone is interested in the measure.
     */
    public static function hasInterestInMeasure(Building $building, InputSource $inputSource, Step $step): bool
    {
        return self::forInputSource($inputSource)
                ->where('user_id', $building->user_id)
                ->where('step_id', $step->id)
                ->where('planned', true)
                ->first() instanceof UserActionPlanAdvice;
    }
}
