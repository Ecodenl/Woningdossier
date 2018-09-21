<?php

namespace App\Models;

use App\Helpers\TranslatableTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\MeasureApplication.
 *
 * @property int $id
 * @property string $measure_type
 * @property string $measure_name
 * @property string $short
 * @property string $application
 * @property float $costs
 * @property string $cost_unit
 * @property float $minimal_costs
 * @property int $maintenance_interval
 * @property string $maintenance_unit
 * @property int $step_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 *
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
}
