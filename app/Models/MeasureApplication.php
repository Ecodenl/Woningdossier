<?php

namespace App\Models;

use App\Helpers\KeyFigures\FloorInsulation\Temperature as FloorInsulationTemperature;
use App\Helpers\KeyFigures\WallInsulation\Temperature as WallInsulationTemperature;
use App\Scopes\VisibleScope;
use App\Traits\HasShortTrait;
use App\Traits\Models\HasTranslations;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\MeasureApplication
 *
 * @property int $id
 * @property string $measure_type
 * @property array $measure_name
 * @property array|null $measure_info
 * @property string $short
 * @property string $application
 * @property float $costs
 * @property array $cost_unit
 * @property float $minimal_costs
 * @property int $maintenance_interval
 * @property array $maintenance_unit
 * @property int $step_id
 * @property array|null $configurations
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read array $translations
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Interest[] $interests
 * @property-read int|null $interests_count
 * @property-read \App\Models\Step $step
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\UserActionPlanAdvice[] $userActionPlanAdvices
 * @property-read int|null $user_action_plan_advices_count
 * @method static \Illuminate\Database\Eloquent\Builder|MeasureApplication newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MeasureApplication newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MeasureApplication query()
 * @method static \Illuminate\Database\Eloquent\Builder|MeasureApplication whereApplication($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MeasureApplication whereConfigurations($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MeasureApplication whereCostUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MeasureApplication whereCosts($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MeasureApplication whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MeasureApplication whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MeasureApplication whereMaintenanceInterval($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MeasureApplication whereMaintenanceUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MeasureApplication whereMeasureInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MeasureApplication whereMeasureName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MeasureApplication whereMeasureType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MeasureApplication whereMinimalCosts($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MeasureApplication whereShort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MeasureApplication whereStepId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MeasureApplication whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class MeasureApplication extends Model
{
    use HasTranslations,
        HasShortTrait;

    const ENERGY_SAVING = 'energy_saving';
    const MAINTENANCE = 'maintenance';

    protected $translatable = [
        'measure_name', 'measure_info', 'cost_unit', 'maintenance_unit',
    ];

    protected $fillable = [
        'measure_name', 'measure_info', 'configurations', 'cost_range', 'savings_money',
    ];

    protected $casts = [
        'cost_range' => 'array',
        'has_calculations' => 'boolean',
        'configurations' => 'array',
    ];

    /**
     * Returns all the interest levels given for the interest.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function interests()
    {
        return $this->morphToMany(Interest::class, 'interested_in', 'user_interests');
//        return $this->morphed(Interest::class, 'interested_in', 'user_interests');
    }

    /**
     * Method to check whether a measure application is an advice.
     */
    public function isAdvice(): bool
    {
        // array of measure shorts that are considered to be advices
        $measureShortsThatAreAdvices = [
            WallInsulationTemperature::WALL_INSULATION_JOINTS,
            WallInsulationTemperature::WALL_INSULATION_FACADE,
            WallInsulationTemperature::WALL_INSULATION_RESEARCH,
            FloorInsulationTemperature::FLOOR_INSULATION_FLOOR,
            FloorInsulationTemperature::FLOOR_INSULATION_BOTTOM,
            FloorInsulationTemperature::FLOOR_INSULATION_RESEARCH,
        ];

        return in_array($this->short, $measureShortsThatAreAdvices);
    }

    public function step()
    {
        return $this->belongsTo(Step::class);
    }

    public function userActionPlanAdvices()
    {
        // We need to retrieve this without the visible tag
        // The visible tag defines whether it should be shown on my plan or not, but for other locations
        // (e.g. the question that adds them) it just defines if it's checked or not
        return $this->morphMany(
            UserActionPlanAdvice::class,
            'user_action_plan_advisable'
        )->withoutGlobalScope(VisibleScope::class);
    }
}
