<?php

namespace App\Models;

use App\Traits\GetMyValuesTrait;
use App\Traits\GetValueTrait;
use App\Traits\ToolSettingTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BuildingRoofType
 *
 * @property int $id
 * @property int $building_id
 * @property int|null $input_source_id
 * @property int $roof_type_id
 * @property int|null $element_value_id
 * @property int|null $roof_surface
 * @property int|null $insulation_roof_surface
 * @property int|null $building_heating_id
 * @property array|null $extra
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Building $building
 * @property-read \App\Models\BuildingHeating|null $buildingHeating
 * @property-read \App\Models\ElementValue|null $elementValue
 * @property-read \App\Models\BuildingHeating|null $heating
 * @property-read \App\Models\InputSource|null $inputSource
 * @property-read \App\Models\ElementValue|null $insulation
 * @property-read \App\Models\MeasureApplication $measureApplication
 * @property-read \App\Models\RoofType $roofType
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingRoofType forMe()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingRoofType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingRoofType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingRoofType query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingRoofType residentInput()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingRoofType whereBuildingHeatingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingRoofType whereBuildingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingRoofType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingRoofType whereElementValueId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingRoofType whereExtra($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingRoofType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingRoofType whereInputSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingRoofType whereInsulationRoofSurface($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingRoofType whereRoofSurface($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingRoofType whereRoofTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingRoofType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BuildingRoofType extends Model
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
        'building_id', 'roof_type_id', 'input_source_id', 'element_value_id',
        'roof_surface', 'insulation_roof_surface', 'zinc_surface',
        'building_heating_id', 'extra',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function building()
    {
        return $this->belongsTo(Building::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function roofType()
    {
        return $this->belongsTo(RoofType::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function elementValue()
    {
        return $this->belongsTo(ElementValue::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function insulation()
    {
        return $this->elementValue();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function heating()
    {
        return $this->belongsTo(BuildingHeating::class, 'building_heating_id');
    }

    public function buildingHeating(){
        return $this->belongsTo(BuildingHeating::class);
    }

    public function measureApplication()
    {
        \Log::critical(__METHOD__ . ": Dit werkt niet!!");
        return $this->belongsTo(MeasureApplication::class);
    }
}
