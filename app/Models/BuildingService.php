<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\GetMyValuesTrait;
use App\Traits\GetValueTrait;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * App\Models\BuildingService
 *
 * @property int $id
 * @property int|null $building_id
 * @property int|null $input_source_id
 * @property int $service_id
 * @property int|null $service_value_id
 * @property array|null $extra
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\Building|null $building
 * @property-read \App\Models\InputSource|null $inputSource
 * @property-read \App\Models\Service $service
 * @property-read \App\Models\ServiceType|null $serviceType
 * @property-read \App\Models\ServiceValue|null $serviceValue
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingService allInputSources()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingService forBuilding(\App\Models\Building|int $building)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingService forInputSource(\App\Models\InputSource $inputSource)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingService forMe(?\App\Models\User $user = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingService forUser(\App\Models\User|int $user)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingService newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingService newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingService query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingService residentInput()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingService whereBuildingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingService whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingService whereExtra($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingService whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingService whereInputSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingService whereServiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingService whereServiceValueId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingService whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BuildingService extends Model implements Auditable
{
    use GetValueTrait,
        GetMyValuesTrait,
        \App\Traits\Models\Auditable;

    protected $fillable = ['service_value_id', 'input_source_id', 'extra', 'building_id', 'service_id'];

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

    public function serviceType(): BelongsTo
    {
        // TODO: Broken
        return $this->belongsTo(ServiceType::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function serviceValue(): BelongsTo
    {
        return $this->belongsTo(ServiceValue::class);
    }
}
