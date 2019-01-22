<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\KeyFigureTemperature
 *
 * @property int $id
 * @property int $measure_application_id
 * @property int|null $insulating_glazing_id
 * @property int $building_heating_id
 * @property float $key_figure
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\KeyFigureTemperature newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\KeyFigureTemperature newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\KeyFigureTemperature query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\KeyFigureTemperature whereBuildingHeatingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\KeyFigureTemperature whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\KeyFigureTemperature whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\KeyFigureTemperature whereInsulatingGlazingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\KeyFigureTemperature whereKeyFigure($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\KeyFigureTemperature whereMeasureApplicationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\KeyFigureTemperature whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class KeyFigureTemperature extends Model
{
}
