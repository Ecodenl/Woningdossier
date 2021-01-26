<?php

namespace App\Models;

use App\Helpers\TranslatableTrait;
use App\Traits\HasShortTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\RoofType.
 *
 * @property int                                                                    $id
 * @property string                                                                 $name
 * @property string                                                                 $short
 * @property int                                                                    $calculate_value
 * @property int|null                                                               $order
 * @property \Illuminate\Support\Carbon|null                                        $created_at
 * @property \Illuminate\Support\Carbon|null                                        $updated_at
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\BuildingFeature[] $buildingFeatures
 * @property int|null                                                               $building_features_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|RoofType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RoofType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RoofType query()
 * @method static \Illuminate\Database\Eloquent\Builder|RoofType translated($attribute, $name, $locale = 'nl')
 * @method static \Illuminate\Database\Eloquent\Builder|RoofType whereCalculateValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoofType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoofType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoofType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoofType whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoofType whereShort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoofType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class RoofType extends Model
{
    use TranslatableTrait;
    use HasShortTrait;

    public function buildingFeatures()
    {
        return $this->hasMany(BuildingFeature::class);
    }
}
