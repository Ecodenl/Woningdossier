<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Helpers\StepHelper;
use App\Scopes\NoGeneralDataScope;
use App\Traits\HasShortTrait;
use App\Traits\Models\HasTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Step
 *
 * @property int $id
 * @property int|null $parent_id
 * @property string $slug
 * @property string $short
 * @property array $name
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $scan_id
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\MeasureApplication> $measureApplications
 * @property-read int|null $measure_applications_count
 * @property-read Step|null $parentStep
 * @property-read \App\Models\QuestionnaireStep|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Questionnaire> $questionnaires
 * @property-read int|null $questionnaires_count
 * @property-read \App\Models\Scan|null $scan
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SubStep> $subSteps
 * @property-read int|null $sub_steps_count
 * @property-read mixed $translations
 * @method static Builder<static>|Step childrenForStep(\App\Models\Step $step)
 * @method static Builder<static>|Step expert()
 * @method static \Database\Factories\StepFactory factory($count = null, $state = [])
 * @method static Builder<static>|Step forScan(\App\Models\Scan $scan)
 * @method static Builder<static>|Step newModelQuery()
 * @method static Builder<static>|Step newQuery()
 * @method static Builder<static>|Step ordered()
 * @method static Builder<static>|Step query()
 * @method static Builder<static>|Step quickScan()
 * @method static Builder<static>|Step recalculable()
 * @method static Builder<static>|Step whereCreatedAt($value)
 * @method static Builder<static>|Step whereId($value)
 * @method static Builder<static>|Step whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static Builder<static>|Step whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static Builder<static>|Step whereLocale(string $column, string $locale)
 * @method static Builder<static>|Step whereLocales(string $column, array $locales)
 * @method static Builder<static>|Step whereName($value)
 * @method static Builder<static>|Step whereOrder($value)
 * @method static Builder<static>|Step whereParentId($value)
 * @method static Builder<static>|Step whereScanId($value)
 * @method static Builder<static>|Step whereShort($value)
 * @method static Builder<static>|Step whereSlug($value)
 * @method static Builder<static>|Step whereUpdatedAt($value)
 * @method static Builder<static>|Step withGeneralData()
 * @method static Builder<static>|Step withoutChildren()
 * @mixin \Eloquent
 */
class Step extends Model
{
    use HasFactory,
        HasShortTrait,
        HasTranslations;

    protected $fillable = ['slug', 'name', 'order'];

    protected $translatable = [
        'name'
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new NoGeneralDataScope());
    }

    public function resolveChildRouteBinding($childType, $value, $field)
    {
        // so this method is supposed to resolve the child route binding (any relationship)
        // which could be any child of the step
        // the sub step has a translatable slug, which is impossible to configure on the routes
        if ($childType === 'subStep' && $field === 'slug'){
            $field = (new SubStep())->getRouteKeyName();
            return parent::resolveChildRouteBinding($childType, $value, $field);
        }
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function isDynamic(): bool
    {
        return in_array($this->short, ['heating']);
    }

    public function scopeWithGeneralData(Builder $query): Builder
    {
        return $query->withoutGlobalScope(NoGeneralDataScope::class);
    }

    public function subSteps(): HasMany
    {
        return $this->hasMany(SubStep::class);
    }

    public function nextStepForScan()
    {
        return $this
            ->scan
            ->steps()
            ->where('order', '>', $this->order)
            ->orderBy('order')
            ->first();
    }

    public function previousStepForScan()
    {
        return $this
            ->scan
            ->steps()
            ->where('order', '<', $this->order)
            ->orderByDesc('order')
            ->first();
    }


    /**
     * Return the parent of the step.
     */
    public function parentStep(): BelongsTo
    {
        return $this->belongsTo(Step::class, 'parent_id', 'id');
    }

    public function scopeChildrenForStep(Builder $query, Step $step)
    {
        return $query->where('parent_id', $step->id);
    }

    /**
     * Method to leave out the sub steps.
     */
    public function scopeWithoutChildren(Builder $query): Builder
    {
        return $query->where('parent_id', null);
    }

    public function questionnaires(): BelongsToMany
    {
        return $this->belongsToMany(Questionnaire::class)
            ->using(QuestionnaireStep::class)
            ->withPivot('order');
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('order', 'asc');
    }

    /** @deprecated Use scopeForScan instead */
    public function scopeQuickScan(Builder $query): Builder
    {
        $quickScan = Scan::quick();
        return $this->scopeForScan($query, $quickScan);
    }

    /** @deprecated Use scopeForScan instead */
    public function scopeExpert(Builder $query): Builder
    {
        $expertScan = Scan::expert();
        return $this->scopeForScan($query, $expertScan);
    }

    public function scopeForScan(Builder $query, Scan $scan): Builder
    {
        return $query->where('scan_id', $scan->id);
    }

    public function scopeRecalculable(Builder $query): Builder
    {
        return $query->where(
            fn (Builder $q) => $q->expert()->orWhere('short', 'small-measures')
        );
    }

    public function scan(): BelongsTo
    {
        return $this->belongsTo(Scan::class);
    }

    /**
     * Get the measure applications from a step.
     */
    public function measureApplications(): HasMany
    {
        return $this->hasMany(MeasureApplication::class);
    }
}
