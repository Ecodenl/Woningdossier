<?php

namespace App\Models;

use App\Helpers\TranslatableTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Measure.
 *
 * @property int                                                                    $id
 * @property string                                                                 $name
 * @property \Illuminate\Support\Carbon|null                                        $created_at
 * @property \Illuminate\Support\Carbon|null                                        $updated_at
 * @property \App\Models\BuildingElement                                            $buildingElements
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\BuildingService[] $buildingServices
 * @property int|null                                                               $building_services_count
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\MeasureCategory[] $categories
 * @property int|null                                                               $categories_count
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\MeasureProperty[] $properties
 * @property int|null                                                               $properties_count
 * @property \App\Models\ServiceType                                                $serviceType
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Measure newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Measure newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Measure query()
 * @method static \Illuminate\Database\Eloquent\Builder|Measure translated($attribute, $name, $locale = 'nl')
 * @method static \Illuminate\Database\Eloquent\Builder|Measure whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Measure whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Measure whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Measure whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Measure extends Model
{
    use TranslatableTrait;

    public function serviceType()
    {
        return $this->belongsTo(ServiceType::class);
    }

    public function buildingElements()
    {
        return $this->belongsTo(BuildingElement::class);
    }

    public function buildingServices()
    {
        return $this->hasMany(BuildingService::class);
    }

    public function categories()
    {
        return $this->belongsToMany(MeasureCategory::class);
    }

    public function properties()
    {
        return $this->hasMany(MeasureProperty::class);
    }
}
