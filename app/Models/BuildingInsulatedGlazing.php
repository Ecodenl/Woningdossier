<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\GetMyValuesTrait;
use App\Traits\GetValueTrait;

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
 * @property string|null $m2
 * @property int|null $windows
 * @property array<array-key, mixed>|null $extra
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Building $building
 * @property-read \App\Models\BuildingHeating|null $buildingHeating
 * @property-read \App\Models\InputSource|null $inputSource
 * @property-read \App\Models\InsulatingGlazing|null $insulatedGlazing
 * @property-read \App\Models\MeasureApplication $measureApplication
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingInsulatedGlazing allInputSources()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingInsulatedGlazing forBuilding(\App\Models\Building|int $building)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingInsulatedGlazing forInputSource(\App\Models\InputSource $inputSource)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingInsulatedGlazing forMe(?\App\Models\User $user = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingInsulatedGlazing forUser(\App\Models\User|int $user)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingInsulatedGlazing newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingInsulatedGlazing newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingInsulatedGlazing query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingInsulatedGlazing residentInput()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingInsulatedGlazing whereBuildingHeatingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingInsulatedGlazing whereBuildingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingInsulatedGlazing whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingInsulatedGlazing whereExtra($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingInsulatedGlazing whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingInsulatedGlazing whereInputSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingInsulatedGlazing whereInsulatingGlazingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingInsulatedGlazing whereM2($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingInsulatedGlazing whereMeasureApplicationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingInsulatedGlazing whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingInsulatedGlazing whereWindows($value)
 * @mixin \Eloquent
 */
class BuildingInsulatedGlazing extends Model
{
    use GetValueTrait;
    use GetMyValuesTrait;
    
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

    protected $crucialRelations = [
        'measure_application_id',
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

    public function measureApplication(): BelongsTo
    {
        return $this->belongsTo(MeasureApplication::class);
    }

    public function insulatedGlazing(): BelongsTo
    {
        return $this->belongsTo(InsulatingGlazing::class, 'insulating_glazing_id', 'id');
    }

    public function buildingHeating(): BelongsTo
    {
        return $this->belongsTo(BuildingHeating::class, 'building_heating_id', 'id');
    }

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }
}
