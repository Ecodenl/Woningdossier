<?php

namespace App\Models;

use App\Traits\GetValueTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BuildingTypeElementMaxSaving.
 *
 * @property int $id
 * @property int $building_type_id
 * @property int $element_id
 * @property int $max_saving
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \App\Models\BuildingType $buildingType
 * @property \App\Models\Element $element
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingTypeElementMaxSaving whereBuildingTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingTypeElementMaxSaving whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingTypeElementMaxSaving whereElementId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingTypeElementMaxSaving whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingTypeElementMaxSaving whereMaxSaving($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingTypeElementMaxSaving whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BuildingTypeElementMaxSaving extends Model
{

    public function buildingType()
    {
        return $this->belongsTo(BuildingType::class);
    }

    public function element()
    {
        return $this->belongsTo(Element::class);
    }
}
