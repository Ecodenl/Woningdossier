<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BuildingElementValue
 *
 * @property-read \App\Models\BuildingElement $buildingElement
 * @mixin \Eloquent
 * @property int $id
 * @property int|null $building_element_id
 * @property string $name
 * @property string $value
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingElementValue whereBuildingElementId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingElementValue whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingElementValue whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingElementValue whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingElementValue whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingElementValue whereValue($value)
 */
class BuildingElementValue extends Model
{
    public function buildingElement(){
    	return $this->belongsTo(BuildingElement::class);
    }
}
