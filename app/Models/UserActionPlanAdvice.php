<?php

namespace App\Models;

use App\Helpers\Hoomdossier;
use App\Helpers\Models\CooperationMeasureApplicationHelper;
use App\Helpers\NumberFormatter;
use App\Scopes\GetValueScope;
use App\Scopes\VisibleScope;
use App\Services\UserActionPlanAdviceService;
use App\Traits\GetMyValuesTrait;
use App\Traits\GetValueTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Collection;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * App\Models\UserActionPlanAdvice
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $input_source_id
 * @property string $user_action_plan_advisable_type
 * @property int $user_action_plan_advisable_id
 * @property string|null $category
 * @property bool $visible
 * @property bool $subsidy_available
 * @property bool $loan_available
 * @property int $order
 * @property array|null $costs
 * @property string|null $savings_gas
 * @property string|null $savings_electricity
 * @property string|null $savings_money
 * @property int|null $year
 * @property bool $planned
 * @property int|null $planned_year
 * @property int|null $step_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\InputSource|null $inputSource
 * @property-read \App\Models\Step|null $step
 * @property-read \App\Models\User $user
 * @property-read Model|\Eloquent $userActionPlanAdvisable
 * @method static Builder|UserActionPlanAdvice allInputSources()
 * @method static Builder|UserActionPlanAdvice category(string $category)
 * @method static Builder|UserActionPlanAdvice cooperationMeasureForType(string $type, \App\Models\InputSource $inputSource)
 * @method static \Database\Factories\UserActionPlanAdviceFactory factory(...$parameters)
 * @method static Builder|UserActionPlanAdvice forAdvisable(\Illuminate\Database\Eloquent\Model $advisable)
 * @method static Builder|UserActionPlanAdvice forBuilding($building)
 * @method static Builder|UserActionPlanAdvice forInputSource(\App\Models\InputSource $inputSource)
 * @method static Builder|UserActionPlanAdvice forMe(?\App\Models\User $user = null)
 * @method static Builder|UserActionPlanAdvice forStep(\App\Models\Step $step)
 * @method static Builder|UserActionPlanAdvice forUser($user)
 * @method static Builder|UserActionPlanAdvice getCategorized()
 * @method static Builder|UserActionPlanAdvice invisible()
 * @method static Builder|UserActionPlanAdvice newModelQuery()
 * @method static Builder|UserActionPlanAdvice newQuery()
 * @method static Builder|UserActionPlanAdvice query()
 * @method static Builder|UserActionPlanAdvice residentInput()
 * @method static Builder|UserActionPlanAdvice whereCategory($value)
 * @method static Builder|UserActionPlanAdvice whereCosts($value)
 * @method static Builder|UserActionPlanAdvice whereCreatedAt($value)
 * @method static Builder|UserActionPlanAdvice whereId($value)
 * @method static Builder|UserActionPlanAdvice whereInputSourceId($value)
 * @method static Builder|UserActionPlanAdvice whereLoanAvailable($value)
 * @method static Builder|UserActionPlanAdvice whereOrder($value)
 * @method static Builder|UserActionPlanAdvice wherePlanned($value)
 * @method static Builder|UserActionPlanAdvice wherePlannedYear($value)
 * @method static Builder|UserActionPlanAdvice whereSavingsElectricity($value)
 * @method static Builder|UserActionPlanAdvice whereSavingsGas($value)
 * @method static Builder|UserActionPlanAdvice whereSavingsMoney($value)
 * @method static Builder|UserActionPlanAdvice whereStepId($value)
 * @method static Builder|UserActionPlanAdvice whereSubsidyAvailable($value)
 * @method static Builder|UserActionPlanAdvice whereUpdatedAt($value)
 * @method static Builder|UserActionPlanAdvice whereUserActionPlanAdvisableId($value)
 * @method static Builder|UserActionPlanAdvice whereUserActionPlanAdvisableType($value)
 * @method static Builder|UserActionPlanAdvice whereUserId($value)
 * @method static Builder|UserActionPlanAdvice whereVisible($value)
 * @method static Builder|UserActionPlanAdvice whereYear($value)
 * @method static Builder|UserActionPlanAdvice withInvisible()
 * @mixin \Eloquent
 */
class UserActionPlanAdvice extends Model implements Auditable
{
    use HasFactory,
        GetValueTrait,
        GetMyValuesTrait,
        \App\Traits\Models\Auditable;

    protected $table = 'user_action_plan_advices';

    protected $fillable = [
        'user_id',
        'input_source_id',
        'user_action_plan_advisable_type',
        'user_action_plan_advisable_id',
        'category',
        'visible',
        'order',
        'costs',
        'savings_gas',
        'savings_electricity',
        'savings_money',
        'year',
        'planned',
        'planned_year',
        'step_id',
        'loan_available',
        'subsidy_available'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'planned' => 'boolean',
        'visible' => 'boolean',
        'subsidy_available' => 'boolean',
        'loan_available' => 'boolean',
        'costs' => 'array',
    ];

    protected array $ignoreAttributes = [
        'loan_available',
        'subsidy_available',
    ];

    public static function boot()
    {
        parent::boot();

        static::addGlobalScope(new VisibleScope());
    }

    # Scopes
    public function scopeGetCategorized(Builder $query): Collection
    {
        $categories = array_values(UserActionPlanAdviceService::getCategories());
        return $query->orderBy('order')->get()->groupBy('category')->sortKeysUsing(function ($a, $b) use ($categories) {
            // https://stackoverflow.com/questions/3737139/reference-what-does-this-symbol-mean-in-php/31298778#31298778
            return array_search($a, $categories) <=> array_search($b, $categories);
        });
    }

    /**
     * Method to scope the advices without its deleted cooperation measure applications and for given type.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $type
     * @param  \App\Models\InputSource  $inputSource
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCooperationMeasureForType(Builder $query, string $type, InputSource $inputSource)
    {
        $isExtensive = $type === CooperationMeasureApplicationHelper::EXTENSIVE_MEASURE;

        return $query->whereHasMorph(
            'userActionPlanAdvisable',
            '*',
            function (Builder $query, $type) use ($isExtensive, $inputSource) {
                // We have to do this, else the results are incorrect.
                // This means that you won't need to call above scope if you're also calling this one.
                $query->withoutGlobalScope(GetValueScope::class)->where('input_source_id', $inputSource->id);
                if ($type === CooperationMeasureApplication::class) {
                    $query->where('is_extensive_measure', $isExtensive);
                }
            }
        );
    }

    public function scopeWithInvisible(Builder $query)
    {
        return $query->withoutGlobalScope(VisibleScope::class);
    }

    public function scopeForAdvisable(Builder $query, Model $advisable): Builder
    {
        return $query->where('user_action_plan_advisable_type', get_class($advisable))
            ->where('user_action_plan_advisable_id', $advisable->id);
    }

    /**
     * Method to only scope the invisible rows
     *
     * @param  Builder  $query
     * @return mixed
     */
    public function scopeInvisible(Builder $query): Builder
    {
        return $query->withInvisible()->where('visible', false);
    }

    /**
     * Scope a query to only include results for the particular step.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForStep($query, Step $step)
    {
        return $query->where('step_id', $step->id);
    }

    public function scopeCategory(Builder $query, string $category)
    {
        return $query->where('category', $category);
    }

    # Relations
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function step(): BelongsTo
    {
        return $this->belongsTo(Step::class);
    }

    public function userActionPlanAdvisable(): MorphTo
    {
        return $this->morphTo();
    }

    # Unsorted

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
            return $prefix.NumberFormatter::format(max($costs['from'] ?? 0, $costs['to'] ?? 0), 0, true);
        }
    }
}
