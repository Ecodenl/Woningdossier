<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\EnergyLabel.
 *
 * @property int $id
 * @property string $name
 * @property string $country_code
 * @property int|null $calculate_value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\BuildingFeature[] $buildingFeatures
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EnergyLabel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EnergyLabel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EnergyLabel query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EnergyLabel whereCalculateValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EnergyLabel whereCountryCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EnergyLabel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EnergyLabel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EnergyLabel whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EnergyLabel whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class EnergyLabel extends Model
{
    public function buildingFeatures()
    {
        return $this->hasMany(BuildingFeature::class);
    }
}
