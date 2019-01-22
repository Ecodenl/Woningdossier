<?php

namespace App\Models;

use App\Helpers\TranslatableTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\RoofType.
 *
 * @property int $id
 * @property string $name
 * @property string $short
 * @property int $calculate_value
 * @property int|null $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\BuildingFeature[] $buildingFeatures
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RoofType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RoofType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RoofType query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RoofType translated($attribute, $name, $locale = 'nl')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RoofType whereCalculateValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RoofType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RoofType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RoofType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RoofType whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RoofType whereShort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RoofType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class RoofType extends Model
{
    use TranslatableTrait;

    public function buildingFeatures()
    {
        return $this->hasMany(BuildingFeature::class);
    }
}
