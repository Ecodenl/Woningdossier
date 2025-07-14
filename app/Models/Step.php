<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
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
 * @property array<array-key, mixed> $name
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
 * @method static \Database\Factories\StepFactory factory($count = null, $state = [])
 * @method static Builder<static>|Step newModelQuery()
 * @method static Builder<static>|Step newQuery()
 * @method static Builder<static>|Step query()
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
 * @mixin \Eloquent
 */
#[ScopedBy([NoGeneralDataScope::class])]
class Step extends Model
{
    use HasFactory,
        HasShortTrait,
        HasTranslations;

    protected $fillable = ['slug', 'name', 'order'];

    protected $translatable = [
        'name',
    ];


    // Static calls
    public static function allSlugs(): array
    {
        return [
            "ventilation",
            "wall-insulation",
            "insulated-glazing",
            "floor-insulation",
            "roof-insulation",
            "high-efficiency-boiler",
            "heat-pump",
            "solar-panels",
            "heater",
            "woninggegevens",
            "bewoners-gebruik",
            "woonwensen",
            "woonstatus",
            "verwarming",
            "kleine-maatregelen",
            "woninggegevens",
            "bewoners-gebruik",
            "woonwensen",
            "woonstatus",
            "kleine-maatregelen",
        ];
    }

    // Model methods
    public function resolveChildRouteBinding($childType, $value, $field): ?Model
    {
        // So this method is supposed to resolve the child route binding (any relationship)
        // which could be any child of the step.
        // the sub step has a translatable slug, which is impossible to configure on the routes, so we ensure
        // we alter the slug to the translatable type.
        if ($childType === 'subStep' && $field === 'slug') {
            $field = (new SubStep())->getRouteKeyName();
        }

        return parent::resolveChildRouteBinding($childType, $value, $field);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function isDynamic(): bool
    {
        return in_array($this->short, ['heating']);
    }

    #[Scope]
    protected function withGeneralData(Builder $query): Builder
    {
        return $query->withoutGlobalScope(NoGeneralDataScope::class);
    }

    public function subSteps(): HasMany
    {
        return $this->hasMany(SubStep::class);
    }

    public function nextStepForScan(): ?Step
    {
        /** @var null|Step */
        return $this
            ->scan
            ->steps()
            ->where('order', '>', $this->order)
            ->orderBy('order')
            ->first();
    }

    public function previousStepForScan(): ?Step
    {
        /** @var null|Step */
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

    #[Scope]
    protected function childrenForStep(Builder $query, Step $step)
    {
        return $query->where('parent_id', $step->id);
    }

    /**
     * Method to leave out the sub steps.
     */
    #[Scope]
    protected function withoutChildren(Builder $query): Builder
    {
        return $query->where('parent_id', null);
    }

    public function questionnaires(): BelongsToMany
    {
        return $this->belongsToMany(Questionnaire::class)
            ->using(QuestionnaireStep::class)
            ->withPivot('order');
    }

    #[Scope]
    protected function ordered(Builder $query): Builder
    {
        return $query->orderBy('order', 'asc');
    }

    /** @deprecated Use scopeForScan instead */
    #[Scope]
    protected function quickScan(Builder $query): Builder
    {
        $quickScan = Scan::quick();
        return $this->forScan($query, $quickScan);
    }

    /** @deprecated Use scopeForScan instead */
    #[Scope]
    protected function expert(Builder $query): Builder
    {
        $expertScan = Scan::expert();
        return $this->forScan($query, $expertScan);
    }

    #[Scope]
    protected function forScan(Builder $query, Scan $scan): Builder
    {
        return $query->where('scan_id', $scan->id);
    }

    #[Scope]
    protected function recalculable(Builder $query): Builder
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
