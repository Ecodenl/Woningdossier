<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\RoofType
 *
 * @property int $id
 * @property string $translation_key
 * @property int $calculate_value
 * @property int|null $order
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\BuildingFeature[] $buildingFeatures
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RoofType whereCalculateValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RoofType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RoofType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RoofType whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RoofType whereTranslationKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RoofType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class RoofType extends Model
{
    public function buildingFeatures(){
    	return $this->hasMany(BuildingFeature::class);
    }
}