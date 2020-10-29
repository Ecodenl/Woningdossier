<?php

namespace App\Models;

use App\Helpers\KeyFigures\FloorInsulation\Temperature as FloorInsulationTemperature;
use App\Helpers\KeyFigures\WallInsulation\Temperature as WallInsulationTemperature;
use App\Helpers\TranslatableTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\MeasureApplication.
 *
 * @property int                             $id
 * @property string                          $measure_type
 * @property string                          $measure_name
 * @property string                          $short
 * @property string                          $application
 * @property float                           $costs
 * @property string                          $cost_unit
 * @property float                           $minimal_costs
 * @property int                             $maintenance_interval
 * @property string                          $maintenance_unit
 * @property int                             $step_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MeasureApplication newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MeasureApplication newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MeasureApplication query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MeasureApplication translated($attribute, $name, $locale = 'nl')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MeasureApplication whereApplication($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MeasureApplication whereCostUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MeasureApplication whereCosts($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MeasureApplication whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MeasureApplication whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MeasureApplication whereMaintenanceInterval($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MeasureApplication whereMaintenanceUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MeasureApplication whereMeasureName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MeasureApplication whereMeasureType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MeasureApplication whereMinimalCosts($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MeasureApplication whereShort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MeasureApplication whereStepId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MeasureApplication whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class MeasureApplication extends Model
{
    use TranslatableTrait;

    /**
     * @param string $short
     *
     * @return MeasureApplication|Model|object|null
     */
    public static function byShort($short)
    {
        return self::where('short', '=', $short)->first();
    }

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
}
