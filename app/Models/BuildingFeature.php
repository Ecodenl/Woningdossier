<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BuildingFeature
 *
 * @property-read \App\Models\Address $address
 * @property-read \App\Models\BuildingCategory $buildingCategory
 * @property-read \App\Models\BuildingType $buildingType
 * @property-read \App\Models\EnergyLabel $energyLabel
 * @property-read \App\Models\ObjectType $objectType
 * @property-read \App\Models\RoofType $roofType
 * @mixin \Eloquent
 * @property int $id
 * @property int|null $address_id
 * @property int|null $object_type_id
 * @property int|null $building_category_id
 * @property int|null $building_type_id
 * @property int|null $roof_type_id
 * @property int|null $energy_label_id
 * @property int|null $surface
 * @property int|null $volume
 * @property int|null $build_year
 * @property int|null $building_layers
 * @property int $monument
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingFeature whereAddressId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingFeature whereBuildYear($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingFeature whereBuildingCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingFeature whereBuildingLayers($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingFeature whereBuildingTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingFeature whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingFeature whereEnergyLabelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingFeature whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingFeature whereMonument($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingFeature whereObjectTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingFeature whereRoofTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingFeature whereSurface($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingFeature whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingFeature whereVolume($value)
 * @property-read \App\Models\Building $building
 */
class BuildingFeature extends Model
{
    public function building(){
    	return $this->belongsTo(Building::class);
    }

    public function objectType(){
    	return $this->belongsTo(ObjectType::class);
    }

    public function buildingCategory(){
    	return $this->belongsTo(BuildingCategory::class);
    }

    public function buildingType(){
    	return $this->belongsTo(BuildingType::class);
    }

    public function roofType(){
    	return $this->belongsTo(RoofType::class);
    }

    public function energyLabel(){
    	return $this->belongsTo(EnergyLabel::class);
    }
}