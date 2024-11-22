<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\GetMyValuesTrait;
use App\Traits\GetValueTrait;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BuildingAppliance
 *
 * @property int $id
 * @property int|null $building_id
 * @property int|null $input_source_id
 * @property int|null $appliance_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Appliance|null $appliance
 * @property-read \App\Models\Building|null $building
 * @property-read \App\Models\InputSource|null $inputSource
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingAppliance allInputSources()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingAppliance forBuilding(\App\Models\Building|int $building)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingAppliance forInputSource(\App\Models\InputSource $inputSource)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingAppliance forMe(?\App\Models\User $user = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingAppliance forUser(\App\Models\User|int $user)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingAppliance newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingAppliance newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingAppliance query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingAppliance residentInput()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingAppliance whereApplianceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingAppliance whereBuildingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingAppliance whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingAppliance whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingAppliance whereInputSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingAppliance whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BuildingAppliance extends Model
{
    use GetValueTrait;
    use GetMyValuesTrait;

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    public function appliance(): BelongsTo
    {
        return $this->belongsTo(Appliance::class);
    }
}
