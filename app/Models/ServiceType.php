<?php

namespace App\Models;

use App\Helpers\TranslatableTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ServiceType.
 *
 * @property int                                                                    $id
 * @property string                                                                 $name
 * @property string                                                                 $iso
 * @property \Illuminate\Support\Carbon|null                                        $created_at
 * @property \Illuminate\Support\Carbon|null                                        $updated_at
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\BuildingService[] $buildingServices
 * @property int|null                                                               $building_services_count
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\Element[]         $elements
 * @property int|null                                                               $elements_count
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\Measure[]         $measures
 * @property int|null                                                               $measures_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceType query()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceType translated($attribute, $name, $locale = 'nl')
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceType whereIso($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceType whereUpdatedAt($value)
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
