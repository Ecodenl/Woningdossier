<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Helpers\Models\CooperationMeasureApplicationHelper;
use App\Scopes\VisibleScope;
use App\Traits\Models\HasMappings;
use App\Traits\Models\HasTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\CooperationMeasureApplication
 *
 * @property int $id
 * @property array $name
 * @property array $info
 * @property array $costs
 * @property string $savings_money
 * @property array $extra
 * @property bool $is_extensive_measure
 * @property bool $is_deletable
 * @property int $cooperation_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Cooperation $cooperation
 * @property-read mixed $translations
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserActionPlanAdvice> $userActionPlanAdvices
 * @property-read int|null $user_action_plan_advices_count
 * @method static Builder|CooperationMeasureApplication extensiveMeasures()
 * @method static \Database\Factories\CooperationMeasureApplicationFactory factory($count = null, $state = [])
 * @method static Builder|CooperationMeasureApplication newModelQuery()
 * @method static Builder|CooperationMeasureApplication newQuery()
 * @method static Builder|CooperationMeasureApplication onlyTrashed()
 * @method static Builder|CooperationMeasureApplication query()
 * @method static Builder|CooperationMeasureApplication smallMeasures()
 * @method static Builder|CooperationMeasureApplication whereCooperationId($value)
 * @method static Builder|CooperationMeasureApplication whereCosts($value)
 * @method static Builder|CooperationMeasureApplication whereCreatedAt($value)
 * @method static Builder|CooperationMeasureApplication whereDeletedAt($value)
 * @method static Builder|CooperationMeasureApplication whereExtra($value)
 * @method static Builder|CooperationMeasureApplication whereId($value)
 * @method static Builder|CooperationMeasureApplication whereInfo($value)
 * @method static Builder|CooperationMeasureApplication whereIsDeletable($value)
 * @method static Builder|CooperationMeasureApplication whereIsExtensiveMeasure($value)
 * @method static Builder|CooperationMeasureApplication whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static Builder|CooperationMeasureApplication whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static Builder|CooperationMeasureApplication whereLocale(string $column, string $locale)
 * @method static Builder|CooperationMeasureApplication whereLocales(string $column, array $locales)
 * @method static Builder|CooperationMeasureApplication whereName($value)
 * @method static Builder|CooperationMeasureApplication whereSavingsMoney($value)
 * @method static Builder|CooperationMeasureApplication whereUpdatedAt($value)
 * @method static Builder|CooperationMeasureApplication withTrashed()
 * @method static Builder|CooperationMeasureApplication withoutTrashed()
 * @mixin \Eloquent
 */
class CooperationMeasureApplication extends Model
{
    use HasFactory,
        HasTranslations,
        SoftDeletes,
        HasMappings;

    protected $translatable = ['name', 'info'];

    protected $fillable = [
        'name', 'info', 'costs', 'savings_money', 'extra', 'cooperation_id', 'is_extensive_measure', 'is_deletable',
    ];

    protected $casts = [
        'costs' => 'array',
        'extra' => 'array',
        'is_extensive_measure' => 'boolean',
        'is_deletable' => 'boolean',
    ];

    # Model Methods
    public function getType(): string
    {
        return $this->is_extensive_measure
            ? CooperationMeasureApplicationHelper::EXTENSIVE_MEASURE
            : CooperationMeasureApplicationHelper::SMALL_MEASURE;
    }

    # Scopes
    public function scopeExtensiveMeasures(Builder $query): Builder
    {
        return $query->where('is_extensive_measure', true);
    }

    public function scopeSmallMeasures(Builder $query): Builder
    {
        return $query->where('is_extensive_measure', false);
    }

    # Relations
    public function userActionPlanAdvices(): MorphMany
    {
        // We need to retrieve this without the visible tag
        // The visible tag defines whether it should be shown on my plan or not, but for other locations
        // (e.g. the question that adds them) it just defines if it's checked or not
        return $this->morphMany(
            UserActionPlanAdvice::class,
            'user_action_plan_advisable'
        )->withoutGlobalScope(VisibleScope::class);
    }

    public function cooperation(): BelongsTo
    {
        return $this->belongsTo(Cooperation::class);
    }
}
