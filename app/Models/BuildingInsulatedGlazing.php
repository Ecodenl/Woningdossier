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
 * @property array|null $extra
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Building $building
 * @property-read \App\Models\BuildingHeating|null $buildingHeating
 * @property-read \App\Models\InputSource|null $inputSource
 * @property-read \App\Models\InsulatingGlazing|null $insulatedGlazing
 * @property-read \App\Models\MeasureApplication $measureApplication
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingInsulatedGlazing allInputSources()
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingInsulatedGlazing forBuilding($building)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingInsulatedGlazing forInputSource(\App\Models\InputSource $inputSource)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingInsulatedGlazing forMe(?\App\Models\User $user = null)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingInsulatedGlazing forUser($user)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingInsulatedGlazing newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingInsulatedGlazing newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingInsulatedGlazing query()
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingInsulatedGlazing residentInput()
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingInsulatedGlazing whereBuildingHeatingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingInsulatedGlazing whereBuildingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingInsulatedGlazing whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingInsulatedGlazing whereExtra($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingInsulatedGlazing whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingInsulatedGlazing whereInputSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingInsulatedGlazing whereInsulatingGlazingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingInsulatedGlazing whereM2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingInsulatedGlazing whereMeasureApplicationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingInsulatedGlazing whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingInsulatedGlazing whereWindows($value)
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
