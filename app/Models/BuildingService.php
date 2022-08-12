<?php

namespace App\Models;

use App\Traits\GetMyValuesTrait;
use App\Traits\GetValueTrait;
use App\Traits\ToolSettingTrait;
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
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\Building|null $building
 * @property-read \App\Models\InputSource|null $inputSource
 * @property-read \App\Models\Service $service
 * @property-read \App\Models\ServiceType $serviceType
 * @property-read \App\Models\ServiceValue|null $serviceValue
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingService allInputSources()
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingService forBuilding($building)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingService forInputSource(\App\Models\InputSource $inputSource)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingService forMe(?\App\Models\User $user = null)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingService forUser($user)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingService newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingService newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingService query()
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingService residentInput()
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingService whereBuildingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingService whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingService whereExtra($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingService whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingService whereInputSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingService whereServiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingService whereServiceValueId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingService whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BuildingService extends Model implements Auditable
{
    use GetValueTrait,
        GetMyValuesTrait,
        ToolSettingTrait,
        \App\Traits\Models\Auditable;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'extra' => 'array',
    ];

    protected $fillable = ['service_value_id', 'input_source_id', 'extra', 'building_id', 'service_id'];

    public function building()
    {
        return $this->belongsTo(Building::class);
    }

    public function serviceType()
    {
        return $this->belongsTo(ServiceType::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function serviceValue()
    {
        return $this->belongsTo(ServiceValue::class);
    }
}
