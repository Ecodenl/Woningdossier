<?php

namespace App\Models;

use App\Helpers\Hoomdossier;
use App\Helpers\NumberFormatter;
use App\Scopes\GetValueScope;
use App\Scopes\VisibleScope;
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
    use GetValueTrait,
        GetMyValuesTrait,
        ToolSettingTrait;

    protected $table = 'user_action_plan_advices';

    public $fillable = [
        'user_id',
        'input_source_id',
        'user_action_plan_advisable_type', 'user_action_plan_advisable_id', 'category', 'visible', 'order', 'costs',
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

        static::addGlobalScope(new VisibleScope());
    }

    /**
     * Method to scope the advices without its deleted cooperation measure applications
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeWithoutDeletedCooperationMeasureApplications(Builder $query, InputSource $inputSource): Builder
    {
        // this works because it boots the cooperation measure application model, which has the soft deletes trait
        return $query->whereHasMorph('userActionPlanAdvisable', [
            CooperationMeasureApplication::class,
            MeasureApplication::class,
            CustomMeasureApplication::class,
        ],
            // cant use scopes.
            fn (Builder $q) => $q->withoutGlobalScope(GetValueScope::class)->where('input_source_id', $inputSource->id)
        );
    }

    public function scopeWithInvisible(Builder $query)
    {
        return $query->withoutGlobalScope(VisibleScope::class);
    }

    /**
     * Method to only scope the invisible rows
     *
     * @param Builder $query
     * @return mixed
     */
    public function scopeInvisible(Builder $query): Builder
    {
        return $query->withInvisible()->where('visible', false);
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

    public function scopeCategory(Builder $query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Check if the costs are a valid range.
     *
     * @return bool
     */
    public function costIsRange(): bool
    {
        $costs = $this->costs;
        return isset($costs['from']) && is_numeric($costs['from']) && isset($costs['to']) && is_numeric($costs['to']);
    }

    /**
     * Get average of the from and to values of the costs.
     *
     * @return int
     */
    public function getCostAverage(): int
    {
        $costs = $this->costs;

        return (($costs['from'] ?? 0) + ($costs['to'] ?? 0)) / 2;
    }

    /**
     * Get the most logical cost value (if not range) and format it accordingly.
     *
     * @param  bool  $range
     * @param  bool  $prefixUnit
     *
     * @return string|void
     */
    public function getCost(bool $range = false, bool $prefixUnit = false)
    {
        $unit = Hoomdossier::getUnitForColumn('costs');
        $prefix = $prefixUnit ? "{$unit} " : '';

        // Get the default formatting for the
        $costs = $this->costs;
        if ($range) {
            NumberFormatter::range($costs['from'] ?? 0, $costs['to'] ?? 0, 0, ' - ', $prefix);
        } else {
            return $prefix . NumberFormatter::format(max($costs['from'] ?? 0, $costs['to'] ?? 0), 0, true);
        }
    }
}
