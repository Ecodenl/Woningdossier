<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Appliance
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\BuildingService[] $buildingServices
 * @property-read int|null $building_services_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ApplianceProperty[] $properties
 * @property-read int|null $properties_count
 * @method static \Illuminate\Database\Eloquent\Builder|Appliance newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Appliance newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Appliance query()
 * @method static \Illuminate\Database\Eloquent\Builder|Appliance whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Appliance whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Appliance whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Appliance whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Appliance extends Model
{
    public function buildingServices()
    {
        return $this->belongsToMany(BuildingService::class);
    }

    public function properties()
    {
        return $this->hasMany(ApplianceProperty::class);
    }
}
