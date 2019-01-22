<?php

namespace App\Models;

use App\Traits\GetMyValuesTrait;
use App\Traits\ToolSettingTrait;
use App\Traits\GetValueTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BuildingAppliance.
 *
 * @property int $id
 * @property int|null $building_id
 * @property int|null $appliance_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \App\Models\Appliance|null $appliance
 * @property \App\Models\Building|null $building
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingAppliance whereApplianceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingAppliance whereBuildingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingAppliance whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingAppliance whereId($value)
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
