<?php

namespace App\Models;

use App\Helpers\TranslatableTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Measure.
 *
 * @property int $id
 * @property string $name
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \App\Models\BuildingElement $buildingElements
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\BuildingService[] $buildingServices
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\MeasureCategory[] $categories
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\MeasureProperty[] $properties
 * @property \App\Models\ServiceType $serviceType
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Measure translated($attribute, $name, $locale = 'nl')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Measure whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Measure whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Measure whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Measure whereUpdatedAt($value)
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
