<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UserEnergyHabit
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $residents_nr
 * @property int|null $thermostat_high
 * @property int|null $thermostat_low
 * @property int|null $hours_high
 * @property int|null $heating_first_floor
 * @property int|null $heating_second_floor
 * @property int|null $heated_space_outside
 * @property int $cook_gas
 * @property int|null $amount_warm_water_id
 * @property int|null $amount_electricity
 * @property int|null $amount_gas
 * @property int|null $amount_water
 * @property int $motivation_comfort
 * @property int $motivation_enviroment
 * @property int $motivation_costs
 * @property int $motivation_investment
 * @property string $motivation_extra
 * @property string|null $start_date
 * @property string|null $end_date
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserEnergyHabit whereAmountElectricity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserEnergyHabit whereAmountGas($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserEnergyHabit whereAmountWarmWaterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserEnergyHabit whereAmountWater($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserEnergyHabit whereCookGas($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserEnergyHabit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserEnergyHabit whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserEnergyHabit whereHeatedSpaceOutside($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserEnergyHabit whereHeatingFirstFloor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserEnergyHabit whereHeatingSecondFloor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserEnergyHabit whereHoursHigh($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserEnergyHabit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserEnergyHabit whereMotivationComfort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserEnergyHabit whereMotivationCosts($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserEnergyHabit whereMotivationEnviroment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserEnergyHabit whereMotivationExtra($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserEnergyHabit whereMotivationInvestment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserEnergyHabit whereResidentsNr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserEnergyHabit whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserEnergyHabit whereThermostatHigh($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserEnergyHabit whereThermostatLow($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserEnergyHabit whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserEnergyHabit whereUserId($value)
 * @mixin \Eloquent
 */
class UserEnergyHabit extends Model
{
    public function user(){
    	return $this->belongsTo(User::class);
    }
}
