<?php

namespace App\Models;

use App\Traits\GetMyValuesTrait;
use App\Traits\GetValueTrait;
use App\Traits\ToolSettingTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UserEnergyHabit
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $input_source_id
 * @property int|null $resident_count
 * @property string|null $thermostat_high
 * @property string|null $thermostat_low
 * @property int|null $hours_high
 * @property int|null $heating_first_floor
 * @property int|null $heating_second_floor
 * @property int|null $heated_space_outside
 * @property int $cook_gas
 * @property int|null $water_comfort_id
 * @property int|null $amount_electricity
 * @property int|null $amount_gas
 * @property int|null $amount_water
 * @property int|null $renovation_plans
 * @property string|null $building_complaints
 * @property string|null $start_date
 * @property string|null $end_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ComfortLevelTapWater|null $comfortLevelTapWater
 * @property-read \App\Models\BuildingHeating|null $heatingFirstFloor
 * @property-read \App\Models\BuildingHeating|null $heatingSecondFloor
 * @property-read \App\Models\InputSource|null $inputSource
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|UserEnergyHabit forInputSource(\App\Models\InputSource $inputSource)
 * @method static \Illuminate\Database\Eloquent\Builder|UserEnergyHabit forMe(\App\Models\User $user = null)
 * @method static \Illuminate\Database\Eloquent\Builder|UserEnergyHabit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserEnergyHabit newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserEnergyHabit query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserEnergyHabit residentInput()
 * @method static \Illuminate\Database\Eloquent\Builder|UserEnergyHabit whereAmountElectricity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserEnergyHabit whereAmountGas($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserEnergyHabit whereAmountWater($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserEnergyHabit whereBuildingComplaints($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserEnergyHabit whereCookGas($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserEnergyHabit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserEnergyHabit whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserEnergyHabit whereHeatedSpaceOutside($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserEnergyHabit whereHeatingFirstFloor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserEnergyHabit whereHeatingSecondFloor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserEnergyHabit whereHoursHigh($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserEnergyHabit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserEnergyHabit whereInputSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserEnergyHabit whereRenovationPlans($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserEnergyHabit whereResidentCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserEnergyHabit whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserEnergyHabit whereThermostatHigh($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserEnergyHabit whereThermostatLow($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserEnergyHabit whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserEnergyHabit whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserEnergyHabit whereWaterComfortId($value)
 * @mixin \Eloquent
 */
class UserEnergyHabit extends Model
{
    use GetValueTrait;
    use GetMyValuesTrait;
    use ToolSettingTrait;

    protected $fillable = [
        'user_id',
        'input_source_id',
        'resident_count',
        'thermostat_high',
        'thermostat_low',
        'hours_high',
        'heating_first_floor',
        'heating_second_floor',
        'cook_gas',
        'water_comfort_id',
        'amount_electricity',
        'amount_gas',
        'amount_water',
        'renovation_plans',
        'building_complaints',
    ];

    /**
     * Get the user that belongsTo this habit.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function heatingFirstFloor()
    {
        return $this->belongsTo(BuildingHeating::class, 'heating_first_floor');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function heatingSecondFloor()
    {
        return $this->belongsTo(BuildingHeating::class, 'heating_second_floor');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function comfortLevelTapWater()
    {
        return $this->belongsTo(ComfortLevelTapWater::class, 'water_comfort_id');
    }
}
