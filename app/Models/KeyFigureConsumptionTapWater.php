<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\KeyFigureConsumptionTapWater
 *
 * @property int $id
 * @property int $comfort_level_tap_water_id
 * @property int $resident_count
 * @property int $water_consumption
 * @property int $energy_consumption
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\ComfortLevelTapWater $comfortLevelTapWater
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\KeyFigureConsumptionTapWater whereComfortLevelTapWaterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\KeyFigureConsumptionTapWater whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\KeyFigureConsumptionTapWater whereEnergyConsumption($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\KeyFigureConsumptionTapWater whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\KeyFigureConsumptionTapWater whereResidentCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\KeyFigureConsumptionTapWater whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\KeyFigureConsumptionTapWater whereWaterConsumption($value)
 * @mixin \Eloquent
 */
class KeyFigureConsumptionTapWater extends Model
{
    public function comfortLevelTapWater(){
    	return $this->belongsTo(ComfortLevelTapWater::class);
    }
}
