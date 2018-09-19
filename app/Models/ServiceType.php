<?php

namespace App\Models;

use App\Helpers\TranslatableTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ServiceType.
 *
 * @property int $id
 * @property string $name
 * @property string $iso
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\BuildingService[] $buildingServices
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\Element[] $elements
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\Measure[] $measures
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ServiceType translated($attribute, $name, $locale = 'nl')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ServiceType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ServiceType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ServiceType whereIso($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ServiceType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ServiceType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ServiceType extends Model
{
    use TranslatableTrait;

    public function measures()
    {
        return $this->hasMany(Measure::class);
    }

    public function elements()
    {
        return $this->hasMany(Element::class);
    }

    public function buildingServices()
    {
        return $this->hasMany(BuildingService::class);
    }
}
