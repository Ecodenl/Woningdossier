<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KeyFigureConsumptionTapWater newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KeyFigureConsumptionTapWater newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KeyFigureConsumptionTapWater query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KeyFigureConsumptionTapWater whereComfortLevelTapWaterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KeyFigureConsumptionTapWater whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KeyFigureConsumptionTapWater whereEnergyConsumption($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KeyFigureConsumptionTapWater whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KeyFigureConsumptionTapWater whereResidentCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KeyFigureConsumptionTapWater whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KeyFigureConsumptionTapWater whereWaterConsumption($value)
 * @mixin \Eloquent
 */
class KeyFigureConsumptionTapWater extends Model
{
    public function comfortLevelTapWater(): BelongsTo
    {
        return $this->belongsTo(ComfortLevelTapWater::class);
    }
}
