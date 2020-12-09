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
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ComfortLevelTapWater $comfortLevelTapWater
 * @method static \Illuminate\Database\Eloquent\Builder|KeyFigureConsumptionTapWater newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|KeyFigureConsumptionTapWater newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|KeyFigureConsumptionTapWater query()
 * @method static \Illuminate\Database\Eloquent\Builder|KeyFigureConsumptionTapWater whereComfortLevelTapWaterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KeyFigureConsumptionTapWater whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KeyFigureConsumptionTapWater whereEnergyConsumption($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KeyFigureConsumptionTapWater whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KeyFigureConsumptionTapWater whereResidentCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KeyFigureConsumptionTapWater whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KeyFigureConsumptionTapWater whereWaterConsumption($value)
 * @mixin \Eloquent
 */
class KeyFigureConsumptionTapWater extends Model
{
    public function comfortLevelTapWater()
    {
        return $this->belongsTo(ComfortLevelTapWater::class);
    }
}
