<?php

namespace App\Models;

use App\Helpers\HoomdossierSession;
use App\Scopes\GetValueScope;
use App\Traits\ToolSettingTrait;
use App\Traits\GetValueTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UserEnergyHabit
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $input_source_id
 * @property int|null $resident_count
 * @property float|null $thermostat_high
 * @property float|null $thermostat_low
 * @property int|null $hours_high
 * @property int|null $heating_first_floor
 * @property int|null $heating_second_floor
 * @property int|null $heated_space_outside
 * @property int $cook_gas
 * @property int|null $water_comfort_id
 * @property int|null $amount_electricity
 * @property int|null $amount_gas
 * @property int|null $amount_water
 * @property string|null $living_situation_extra
 * @property string|null $motivation_extra
 * @property string|null $start_date
 * @property string|null $end_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ComfortLevelTapWater|null $comfortLevelTapWater
 * @property-read \App\Models\BuildingHeating|null $heatingFirstFloor
 * @property-read \App\Models\BuildingHeating|null $heatingSecondFloor
 * @property-read \App\Models\InputSource|null $inputSource
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserEnergyHabit forMe()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserEnergyHabit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserEnergyHabit newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserEnergyHabit query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserEnergyHabit whereAmountElectricity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserEnergyHabit whereAmountGas($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserEnergyHabit whereAmountWater($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserEnergyHabit whereCookGas($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserEnergyHabit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserEnergyHabit whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserEnergyHabit whereHeatedSpaceOutside($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserEnergyHabit whereHeatingFirstFloor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserEnergyHabit whereHeatingSecondFloor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserEnergyHabit whereHoursHigh($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserEnergyHabit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserEnergyHabit whereInputSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserEnergyHabit whereLivingSituationExtra($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserEnergyHabit whereMotivationExtra($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserEnergyHabit whereResidentCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserEnergyHabit whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserEnergyHabit whereThermostatHigh($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserEnergyHabit whereThermostatLow($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserEnergyHabit whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserEnergyHabit whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserEnergyHabit whereWaterComfortId($value)
 * @mixin \Eloquent
 */
class UserEnergyHabit extends Model
{
    use GetValueTrait, ToolSettingTrait;

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
        'living_situation_extra',
        'motivation_extra',
    ];

    /**
     * Normally we would use the GetMyValuesTrait, but that uses the building_id to query on.
     * The UserEnergyHabit uses the user_id instead of the building_id.
     *
     * @param $query
     *
     * @return mixed
     */
    public function scopeForMe($query)
    {
        $building = Building::find(HoomdossierSession::getBuilding());

	    return $query->withoutGlobalScope(GetValueScope::class)
	                 ->join('input_sources', $this->getTable().'.input_source_id', '=', 'input_sources.id')
	                 ->orderBy('input_sources.order', 'ASC')
	                 ->where('user_id', $building->user_id)
	                 ->select([$this->getTable().'.*']);
    }

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
    public function inputSource()
    {
        return $this->belongsTo(InputSource::class);
    }

    /**
     * Get a input source name.
     *
     * @return string InputSource name
     */
    public function getInputSourceName()
    {
        return $this->inputSource()->first()->name;
    }

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
