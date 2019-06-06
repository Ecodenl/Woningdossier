<?php

namespace App\Models;

use App\Traits\GetMyValuesTrait;
use App\Traits\GetValueTrait;
use App\Traits\ToolSettingTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BuildingInsulatedGlazing
 *
 * @property int $id
 * @property int $building_id
 * @property int|null $input_source_id
 * @property int $measure_application_id
 * @property int|null $insulating_glazing_id
 * @property int|null $building_heating_id
 * @property int|null $m2
 * @property int|null $windows
 * @property array|null $extra
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Building $building
 * @property-read \App\Models\BuildingHeating|null $buildingHeating
 * @property-read \App\Models\InputSource|null $inputSource
 * @property-read \App\Models\InsulatingGlazing $insulatedGlazing
 * @property-read \App\Models\MeasureApplication $measureApplication
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingInsulatedGlazing forMe()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingInsulatedGlazing newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingInsulatedGlazing newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingInsulatedGlazing query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingInsulatedGlazing whereBuildingHeatingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingInsulatedGlazing whereBuildingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingInsulatedGlazing whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingInsulatedGlazing whereExtra($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingInsulatedGlazing whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingInsulatedGlazing whereInputSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingInsulatedGlazing whereInsulatingGlazingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingInsulatedGlazing whereM2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingInsulatedGlazing whereMeasureApplicationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingInsulatedGlazing whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingInsulatedGlazing whereWindows($value)
 * @mixin \Eloquent
 */
class BuildingInsulatedGlazing extends Model
{
    use GetValueTrait, GetMyValuesTrait, ToolSettingTrait;
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'extra' => 'array',
    ];

    protected $fillable = [
        'building_id',
        'input_source_id',
        'measure_application_id',
        'insulating_glazing_id',
        'building_heating_id',
        'm2',
        'windows',
        'extra',
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

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function building()
    {
        return $this->belongsTo(Building::class);
    }
}
