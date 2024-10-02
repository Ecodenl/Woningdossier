<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\KeyFigureTemperature
 *
 * @property int $id
 * @property int $measure_application_id
 * @property int|null $insulating_glazing_id
 * @property int $building_heating_id
 * @property string $key_figure
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\BuildingHeating $buildingHeating
 * @property-read \App\Models\InsulatingGlazing|null $insulatingGlazing
 * @property-read \App\Models\MeasureApplication $measureApplication
 * @method static \Illuminate\Database\Eloquent\Builder|KeyFigureTemperature newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|KeyFigureTemperature newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|KeyFigureTemperature query()
 * @method static \Illuminate\Database\Eloquent\Builder|KeyFigureTemperature whereBuildingHeatingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KeyFigureTemperature whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KeyFigureTemperature whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KeyFigureTemperature whereInsulatingGlazingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KeyFigureTemperature whereKeyFigure($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KeyFigureTemperature whereMeasureApplicationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KeyFigureTemperature whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class KeyFigureTemperature extends Model
{
    public function measureApplication(): BelongsTo
    {
        return $this->belongsTo(MeasureApplication::class);
    }

    public function insulatingGlazing(): BelongsTo
    {
        return $this->belongsTo(InsulatingGlazing::class);
    }

    public function buildingHeating(): BelongsTo
    {
        return $this->belongsTo(BuildingHeating::class);
    }
}
