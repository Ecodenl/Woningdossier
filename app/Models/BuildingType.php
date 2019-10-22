<?php

namespace App\Models;

use App\Helpers\TranslatableTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BuildingType.
 *
 * @property int                                                                    $id
 * @property string                                                                 $name
 * @property int                                                                    $calculate_value
 * @property \Illuminate\Support\Carbon|null                                        $created_at
 * @property \Illuminate\Support\Carbon|null                                        $updated_at
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\BuildingFeature[] $buildingFeatures
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingType query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingType translated($attribute, $name, $locale = 'nl')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingType whereCalculateValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BuildingType extends Model
{
    use TranslatableTrait;

    public function buildingFeatures()
    {
        return $this->hasMany(BuildingFeature::class);
    }
}
