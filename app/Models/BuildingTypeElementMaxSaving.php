<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BuildingTypeElementMaxSaving.
 *
 * @property int                             $id
 * @property int                             $building_type_id
 * @property int                             $element_id
 * @property int                             $max_saving
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \App\Models\BuildingType        $buildingType
 * @property \App\Models\Element             $element
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingTypeElementMaxSaving newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingTypeElementMaxSaving newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingTypeElementMaxSaving query()
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
