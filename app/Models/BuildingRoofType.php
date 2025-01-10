<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\GetMyValuesTrait;
use App\Traits\GetValueTrait;

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
 * @property int|null $zinc_surface
 * @property int|null $building_heating_id
 * @property array<array-key, mixed>|null $extra
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Building $building
 * @property-read \App\Models\BuildingHeating|null $buildingHeating
 * @property-read \App\Models\ElementValue|null $elementValue
 * @property-read \App\Models\BuildingHeating|null $heating
 * @property-read \App\Models\InputSource|null $inputSource
 * @property-read \App\Models\ElementValue|null $insulation
 * @property-read \App\Models\MeasureApplication|null $measureApplication
 * @property-read \App\Models\RoofType $roofType
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingRoofType allInputSources()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingRoofType forBuilding(\App\Models\Building|int $building)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingRoofType forInputSource(\App\Models\InputSource $inputSource)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingRoofType forMe(?\App\Models\User $user = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingRoofType forUser(\App\Models\User|int $user)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingRoofType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingRoofType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingRoofType query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingRoofType residentInput()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingRoofType whereBuildingHeatingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingRoofType whereBuildingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingRoofType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingRoofType whereElementValueId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingRoofType whereExtra($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingRoofType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingRoofType whereInputSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingRoofType whereInsulationRoofSurface($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingRoofType whereRoofSurface($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingRoofType whereRoofTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingRoofType whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingRoofType whereZincSurface($value)
 * @mixin \Eloquent
 */
class BuildingRoofType extends Model
{
    use GetValueTrait;
    use GetMyValuesTrait;
    

    protected $fillable = [
        'building_id', 'roof_type_id', 'input_source_id', 'element_value_id',
        'roof_surface', 'insulation_roof_surface', 'zinc_surface',
        'building_heating_id', 'extra',
    ];

    protected $crucialRelations = [
        'roof_type_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'extra' => 'array',
        ];
    }

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    public function roofType(): BelongsTo
    {
        return $this->belongsTo(RoofType::class);
    }

    public function elementValue(): BelongsTo
    {
        return $this->belongsTo(ElementValue::class);
    }

    public function insulation(): BelongsTo
    {
        return $this->elementValue();
    }

    public function heating(): BelongsTo
    {
        return $this->belongsTo(BuildingHeating::class, 'building_heating_id');
    }

    public function buildingHeating(): BelongsTo
    {
        return $this->belongsTo(BuildingHeating::class);
    }

    public function measureApplication(): BelongsTo
    {
        \Log::critical(__METHOD__ . ': Dit werkt niet!!');

        return $this->belongsTo(MeasureApplication::class);
    }
}
