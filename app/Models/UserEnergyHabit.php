<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\GetMyValuesTrait;
use App\Traits\GetValueTrait;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * App\Models\UserEnergyHabit
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $input_source_id
 * @property int|null $resident_count
 * @property numeric|null $thermostat_high
 * @property numeric|null $thermostat_low
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
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\ComfortLevelTapWater|null $comfortLevelTapWater
 * @property-read \App\Models\BuildingHeating|null $heatingFirstFloor
 * @property-read \App\Models\BuildingHeating|null $heatingSecondFloor
 * @property-read \App\Models\InputSource|null $inputSource
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserEnergyHabit allInputSources()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserEnergyHabit forBuilding(\App\Models\Building|int $building)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserEnergyHabit forInputSource(\App\Models\InputSource $inputSource)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserEnergyHabit forMe(?\App\Models\User $user = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserEnergyHabit forUser(\App\Models\User|int $user)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserEnergyHabit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserEnergyHabit newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserEnergyHabit query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserEnergyHabit residentInput()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserEnergyHabit whereAmountElectricity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserEnergyHabit whereAmountGas($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserEnergyHabit whereAmountWater($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserEnergyHabit whereBuildingComplaints($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserEnergyHabit whereCookGas($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserEnergyHabit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserEnergyHabit whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserEnergyHabit whereHeatedSpaceOutside($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserEnergyHabit whereHeatingFirstFloor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserEnergyHabit whereHeatingSecondFloor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserEnergyHabit whereHoursHigh($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserEnergyHabit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserEnergyHabit whereInputSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserEnergyHabit whereRenovationPlans($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserEnergyHabit whereResidentCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserEnergyHabit whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserEnergyHabit whereThermostatHigh($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserEnergyHabit whereThermostatLow($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserEnergyHabit whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserEnergyHabit whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserEnergyHabit whereWaterComfortId($value)
 * @mixin \Eloquent
 */
class UserEnergyHabit extends Model implements Auditable
{
    use GetValueTrait,
        GetMyValuesTrait,
        \App\Traits\Models\Auditable;

    protected $fillable = [
        'user_id',
        'input_source_id',
        'resident_count',
        'thermostat_high',
        'thermostat_low',
        'hours_high',
        'heating_first_floor',
        'heating_second_floor',
        'water_comfort_id',
        'amount_electricity',
        'amount_gas',
        'amount_water',
        'renovation_plans',
        'building_complaints',
    ];

    /**
     * Get the user that belongsTo this habit.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function heatingFirstFloor(): BelongsTo
    {
        return $this->belongsTo(BuildingHeating::class, 'heating_first_floor');
    }

    public function heatingSecondFloor(): BelongsTo
    {
        return $this->belongsTo(BuildingHeating::class, 'heating_second_floor');
    }

    public function comfortLevelTapWater(): BelongsTo
    {
        return $this->belongsTo(ComfortLevelTapWater::class, 'water_comfort_id');
    }
}
