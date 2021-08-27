<?php

namespace App\Models;

use App\Traits\GetMyValuesTrait;
use App\Traits\GetValueTrait;
use App\Traits\ToolSettingTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * App\Models\UserActionPlanAdvice
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $input_source_id
 * @property int $measure_application_id
 * @property string|null $costs
 * @property string|null $savings_gas
 * @property string|null $savings_electricity
 * @property string|null $savings_money
 * @property int|null $year
 * @property bool $planned
 * @property int|null $planned_year
 * @property int $step_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\InputSource|null $inputSource
 * @property-read \App\Models\MeasureApplication $measureApplication
 * @property-read \App\Models\Step $step
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|UserActionPlanAdvice allInputSources()
 * @method static \Illuminate\Database\Eloquent\Builder|UserActionPlanAdvice forBuilding(\App\Models\Building $building)
 * @method static \Illuminate\Database\Eloquent\Builder|UserActionPlanAdvice forInputSource(\App\Models\InputSource $inputSource)
 * @method static \Illuminate\Database\Eloquent\Builder|UserActionPlanAdvice forMe(?\App\Models\User $user = null)
 * @method static \Illuminate\Database\Eloquent\Builder|UserActionPlanAdvice forStep(\App\Models\Step $step)
 * @method static \Illuminate\Database\Eloquent\Builder|UserActionPlanAdvice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserActionPlanAdvice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserActionPlanAdvice query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserActionPlanAdvice residentInput()
 * @method static \Illuminate\Database\Eloquent\Builder|UserActionPlanAdvice whereCosts($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserActionPlanAdvice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserActionPlanAdvice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserActionPlanAdvice whereInputSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserActionPlanAdvice whereMeasureApplicationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserActionPlanAdvice wherePlanned($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserActionPlanAdvice wherePlannedYear($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserActionPlanAdvice whereSavingsElectricity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserActionPlanAdvice whereSavingsGas($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserActionPlanAdvice whereSavingsMoney($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserActionPlanAdvice whereStepId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserActionPlanAdvice whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserActionPlanAdvice whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserActionPlanAdvice whereYear($value)
 * @mixin \Eloquent
 */
class UserActionPlanAdvice extends Model
{
    use GetValueTrait;
    use GetMyValuesTrait;
    use ToolSettingTrait;

    protected $table = 'user_action_plan_advices';

    public $fillable = [
        'user_id',
        'input_source_id',
        'user_action_plan_advisable_type', 'user_action_plan_advisable_id', 'category', 'visible', 'costs',
        'savings_gas', 'savings_electricity', 'savings_money', 'year', 'planned', 'planned_year', 'step_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'planned' => 'boolean',
        'visible' => 'boolean',
        'costs' => 'array',
    ];

    public static function boot()
    {
        parent::boot();

        static::addGlobalScope('visible', function (Builder $builder) {
            $builder->where('visible', true);
        });
    }

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

    public function step()
    {
        return $this->belongsTo(Step::class);
    }

    public function userActionPlanAdvisable(): MorphTo
    {
        return $this->morphTo();
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
