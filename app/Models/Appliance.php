<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Appliance.
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\BuildingService[] $buildingServices
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\ApplianceProperty[] $properties
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Appliance newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Appliance newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Appliance query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Appliance whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Appliance whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Appliance whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Appliance whereUpdatedAt($value)
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
