<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BuildingTypeElementMaxSaving
 *
 * @property int $id
 * @property int $building_type_id
 * @property int $element_id
 * @property int $max_saving
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\BuildingType $buildingType
 * @property-read \App\Models\Element $element
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingTypeElementMaxSaving newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingTypeElementMaxSaving newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingTypeElementMaxSaving query()
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingTypeElementMaxSaving whereBuildingTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingTypeElementMaxSaving whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingTypeElementMaxSaving whereElementId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingTypeElementMaxSaving whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingTypeElementMaxSaving whereMaxSaving($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingTypeElementMaxSaving whereUpdatedAt($value)
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
