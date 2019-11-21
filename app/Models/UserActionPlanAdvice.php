<?php

namespace App\Models;

use App\Services\UserActionPlanAdviceService;
use App\Services\UserInterestService;
use App\Traits\GetMyValuesTrait;
use App\Traits\GetValueTrait;
use App\Traits\ToolSettingTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UserActionPlanAdvice.
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $input_source_id
 * @property int $measure_application_id
 * @property float|null $costs
 * @property float|null $savings_gas
 * @property float|null $savings_electricity
 * @property float|null $savings_money
 * @property int|null $year
 * @property bool $planned
 * @property int|null $planned_year
 * @property int $step_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \App\Models\InputSource|null $inputSource
 * @property \App\Models\MeasureApplication $measureApplication
 * @property \App\Models\Step $step
 * @property \App\Models\User $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserActionPlanAdvice forInputSource(\App\Models\InputSource $inputSource)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserActionPlanAdvice forMe(\App\Models\User $user = null)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserActionPlanAdvice forStep(\App\Models\Step $step)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserActionPlanAdvice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserActionPlanAdvice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserActionPlanAdvice query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserActionPlanAdvice residentInput()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserActionPlanAdvice whereCosts($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserActionPlanAdvice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserActionPlanAdvice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserActionPlanAdvice whereInputSourceId($value)
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
    use GetValueTrait, GetMyValuesTrait, ToolSettingTrait;

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
     * @param Step $step
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

    public function getAdviceYear(InputSource $inputSource = null)
    {

    }

    /**
     * Method to return a year for the personal plan.
     *
     * @return array|int|string|null
     */
    public function getYear(InputSource $inputSource)
    {
        $year = isset($this->planned_year) ? $this->planned_year : $this->year;

        if (is_null($year)) {
            $year = UserActionPlanAdviceService::getAdviceYear($this) ?? __('woningdossier.cooperation.tool.my-plan.no-year');
        }

        return $year;
    }

    /**
     * Check whether someone is interested in the measure.
     *
     * @param Building $building
     * @param InputSource $inputSource
     * @param Step $step
     *
     * @return bool
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
