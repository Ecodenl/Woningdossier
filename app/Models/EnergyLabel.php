<?php

namespace App\Models;

use App\Traits\Models\HasOrder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\EnergyLabel
 *
 * @property int $id
 * @property string $name
 * @property string $country_code
 * @property int|null $calculate_value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\BuildingFeature[] $buildingFeatures
 * @property-read int|null $building_features_count
 * @method static \Illuminate\Database\Eloquent\Builder|EnergyLabel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EnergyLabel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EnergyLabel query()
 * @method static \Illuminate\Database\Eloquent\Builder|EnergyLabel whereCalculateValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EnergyLabel whereCountryCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EnergyLabel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EnergyLabel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EnergyLabel whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EnergyLabel whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class EnergyLabel extends Model
{
    use HasOrder;

    public function buildingFeatures()
    {
        return $this->hasMany(BuildingFeature::class);
    }
}
