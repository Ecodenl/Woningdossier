<?php

namespace App\Models;

use App\Traits\HasShortTrait;
use App\Traits\Models\HasTranslations;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\RoofType
 *
 * @property int $id
 * @property string $name
 * @property string $short
 * @property int $calculate_value
 * @property int|null $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\BuildingFeature[] $buildingFeatures
 * @property-read int|null $building_features_count
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
    use HasShortTrait,
        HasTranslations;

    const PRIMARY_TO_SECONDARY_MAP = [
        'pitched' => 'pitched',
        'flat' => 'flat',
        'none' => 'none',
        'gabled-roof' => 'pitched',
        'rounded-roof' => 'pitched',
        'straw-roof' => 'pitched',
    ];

    const SECONDARY_ROOF_TYPE_SHORTS = [
        'pitched', 'flat', 'none',
    ];

    const MEASURE_MAP = [
        'pitched' => [
            'roof-insulation-flat-current' => 'roof-insulation-pitched-inside',
            'roof-insulation-flat-replace-current' => 'roof-insulation-pitched-replace-tiles',
        ],
        'flat' => [
            'roof-insulation-pitched-inside' => 'roof-insulation-flat-current',
            'roof-insulation-pitched-replace-tiles' => 'roof-insulation-flat-replace-current',
        ],
    ];

    protected $translatable = [
        'name',
    ];

    public function buildingFeatures()
    {
        return $this->hasMany(BuildingFeature::class);
    }
}
