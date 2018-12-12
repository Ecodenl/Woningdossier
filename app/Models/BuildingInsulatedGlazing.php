<?php

namespace App\Models;

use App\Traits\GetValueTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BuildingInsulatedGlazing.
 *
 * @property int $id
 * @property int $building_id
 * @property int $measure_application_id
 * @property int|null $insulating_glazing_id
 * @property int|null $building_heating_id
 * @property int|null $m2
 * @property int|null $windows
 * @property array $extra
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \App\Models\BuildingHeating|null $buildingHeating
 * @property \App\Models\InsulatingGlazing $insulatedGlazing
 * @property \App\Models\MeasureApplication $measureApplication
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingInsulatedGlazing whereBuildingHeatingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingInsulatedGlazing whereBuildingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingInsulatedGlazing whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingInsulatedGlazing whereExtra($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingInsulatedGlazing whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingInsulatedGlazing whereInsulatingGlazingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingInsulatedGlazing whereM2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingInsulatedGlazing whereMeasureApplicationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingInsulatedGlazing whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingInsulatedGlazing whereWindows($value)
 * @mixin \Eloquent
 */
class BuildingInsulatedGlazing extends Model
{
    use GetValueTrait;
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'extra' => 'array',
    ];

    protected $fillable = [
        'building_id', 'input_source_id', 'measure_application_id', 'insulating_glazing_id', 'building_heating_id', 'm2', 'windows', 'extra'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function measureApplication()
    {
        return $this->belongsTo(MeasureApplication::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function insulatedGlazing()
    {
        return $this->belongsTo(InsulatingGlazing::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function buildingHeating()
    {
        return $this->belongsTo(BuildingHeating::class);
    }
}
