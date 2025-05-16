<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
 * @property array<array-key, mixed>|null $how
 * @property array<array-key, mixed>|null $living_situation
 * @property array<array-key, mixed>|null $usage
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Building $building
 * @property-read \App\Models\TFactory|null $use_factory
 * @property-read \App\Models\InputSource|null $inputSource
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingVentilation allInputSources()
 * @method static \Database\Factories\BuildingVentilationFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingVentilation forBuilding(\App\Models\Building|int $building)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingVentilation forInputSource(\App\Models\InputSource $inputSource)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingVentilation forMe(?\App\Models\User $user = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingVentilation forUser(\App\Models\User|int $user)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingVentilation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingVentilation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingVentilation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingVentilation residentInput()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingVentilation whereBuildingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingVentilation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingVentilation whereHow($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingVentilation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingVentilation whereInputSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingVentilation whereLivingSituation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingVentilation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingVentilation whereUsage($value)
 * @mixin \Eloquent
 */
class BuildingVentilation extends Model
{
    use HasFactory;

    use GetMyValuesTrait;
    use GetValueTrait;

    protected $fillable = [
        'building_id', 'input_source_id', 'how', 'living_situation', 'usage',
    ];

    protected function casts(): array
    {
        return [
            'how'              => 'array',
            'living_situation' => 'array',
            'usage'            => 'array',
        ];
    }

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }
}
