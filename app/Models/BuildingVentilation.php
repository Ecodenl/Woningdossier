<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\GetMyValuesTrait;
use App\Traits\GetValueTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BuildingVentilation
 *
 * @property int $id
 * @property int|null $input_source_id
 * @property int $building_id
 * @property array|null $how
 * @property array|null $living_situation
 * @property array|null $usage
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Building $building
 * @property-read \App\Models\InputSource|null $inputSource
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingVentilation allInputSources()
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingVentilation forBuilding($building)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingVentilation forInputSource(\App\Models\InputSource $inputSource)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingVentilation forMe(?\App\Models\User $user = null)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingVentilation forUser($user)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingVentilation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingVentilation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingVentilation query()
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingVentilation residentInput()
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingVentilation whereBuildingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingVentilation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingVentilation whereHow($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingVentilation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingVentilation whereInputSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingVentilation whereLivingSituation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingVentilation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingVentilation whereUsage($value)
 * @mixin \Eloquent
 */
class BuildingVentilation extends Model
{
    use HasFactory;

    use GetMyValuesTrait;
    use GetValueTrait;

    protected $casts = [
        'how'              => 'array',
        'living_situation' => 'array',
        'usage'            => 'array',
    ];

    protected $fillable = [
        'building_id', 'input_source_id', 'how', 'living_situation', 'usage',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function building()
    {
        return $this->belongsTo(Building::class);
    }
}
