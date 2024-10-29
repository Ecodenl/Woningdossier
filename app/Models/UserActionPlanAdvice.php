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
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
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
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\InputSource|null $inputSource
 * @property-read \App\Models\Step|null $step
 * @property-read \App\Models\User $user
 * @property-read Model|\Eloquent $userActionPlanAdvisable
 * @method static Builder|UserActionPlanAdvice allInputSources()
 * @method static Builder|UserActionPlanAdvice category(string $category)
 * @method static Builder|UserActionPlanAdvice cooperationMeasureForType(string $type, \App\Models\InputSource $inputSource)
 * @method static \Database\Factories\UserActionPlanAdviceFactory factory($count = null, $state = [])
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
#[ScopedBy(VisibleScope::class)]
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

    // Scopes
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
     */
    public function scopeCooperationMeasureForType(Builder $query, string $type, InputSource $inputSource): Builder
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
     */
    public function scopeInvisible(Builder $query): Builder
    {
        return $query->withInvisible()->where('visible', false);
    }

    /**
     * Scope a query to only include results for the particular step.
     */
    public function scopeForStep(Builder $query, Step $step): Builder
    {
        return $query->where('step_id', $step->id);
    }

    public function scopeCategory(Builder $query, string $category)
    {
        return $query->where('category', $category);
    }

    // Relations
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
}
