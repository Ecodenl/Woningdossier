<?php

namespace App\Models;

use App\Traits\GetMyValuesTrait;
use App\Traits\ToolSettingTrait;
use App\Traits\GetValueTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BuildingRoofType.
 *
 * @property int $id
 * @property int $building_id
 * @property int $roof_type_id
 * @property int|null $element_value_id
 * @property int|null $surface
 * @property int|null $building_heating_id
 * @property array $extra
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \App\Models\Building $building
 * @property \App\Models\ElementValue|null $elementValue
 * @property \App\Models\BuildingHeating $heating
 * @property \App\Models\RoofType $roofType
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingRoofType whereBuildingHeatingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingRoofType whereBuildingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingRoofType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingRoofType whereElementValueId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingRoofType whereExtra($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingRoofType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingRoofType whereRoofTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingRoofType whereSurface($value)
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
        'building_id', 'roof_type_id', 'input_source_id', 'element_value_id', 'roof_surface', 'building_heating_id', 'extra', 'insulation_roof_surface',
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
        return $this->belongsTo(BuildingHeating::class);
    }
}
