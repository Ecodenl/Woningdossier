<?php

namespace App\Models;

use App\Traits\GetMyValuesTrait;
use App\Traits\GetValueTrait;
use App\Traits\ToolSettingTrait;
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
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingAppliance forInputSource(\App\Models\InputSource $inputSource)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingAppliance forMe(\App\Models\User $user = null)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingAppliance newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingAppliance newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingAppliance query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingAppliance residentInput()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingAppliance whereApplianceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingAppliance whereBuildingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingAppliance whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingAppliance whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingAppliance whereInputSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingAppliance whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BuildingAppliance extends Model
{
    use GetValueTrait, GetMyValuesTrait, ToolSettingTrait;

    public function building()
    {
        return $this->belongsTo(Building::class);
    }

    public function appliance()
    {
        return $this->belongsTo(Appliance::class);
    }
}
