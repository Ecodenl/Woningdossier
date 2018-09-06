<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BuildingFeature
 *
 * @property int $id
 * @property int|null $building_id
 * @property int|null $building_category_id
 * @property int|null $building_type_id
 * @property int|null $roof_type_id
 * @property int|null $energy_label_id
 * @property int|null $cavity_wall
 * @property float|null $wall_surface
 * @property int|null $facade_plastered_painted
 * @property int|null $wall_joints
 * @property int|null $contaminated_wall_joints
 * @property int|null $element_values
 * @property int|null $facade_plastered_surface_id
 * @property int|null $facade_damaged_paintwork_id
 * @property float|null $surface
 * @property float|null $window_surface
 * @property float|null $floor_surface
 * @property int|null $volume
 * @property int|null $build_year
 * @property int|null $building_layers
 * @property int $monument
 * @property string|null $additional_info
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\Building|null $building
 * @property-read \App\Models\BuildingCategory|null $buildingCategory
 * @property-read \App\Models\BuildingType|null $buildingType
 * @property-read \App\Models\EnergyLabel|null $energyLabel
 * @property-read \App\Models\RoofType|null $roofType
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingFeature whereAdditionalInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingFeature whereBuildYear($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingFeature whereBuildingCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingFeature whereBuildingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingFeature whereBuildingLayers($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingFeature whereBuildingTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingFeature whereCavityWall($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingFeature whereContaminatedWallJoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingFeature whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingFeature whereElementValues($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingFeature whereEnergyLabelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingFeature whereFacadeDamagedPaintworkId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingFeature whereFacadePlasteredPainted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingFeature whereFacadePlasteredSurfaceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingFeature whereFloorSurface($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingFeature whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingFeature whereMonument($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingFeature whereRoofTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingFeature whereSurface($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingFeature whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingFeature whereVolume($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingFeature whereWallJoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingFeature whereWallSurface($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingFeature whereWindowSurface($value)
 * @mixin \Eloquent
 */
class BuildingFeature extends Model
{

    protected $fillable = [
        'element_values',
        'plastered_wall_surface',
        'wall_joints',
        'cavity_wall',
        'contaminated_wall_joints',
        'wall_surface',
        'insulation_wall_surface',
        'damage_paintwork',
        'additional_info',
	    'surface',
	    'floor_surface',
	    'build_year',
        'facade_plastered_painted',
        'window_surface'
    ];

    public function building(){
    	return $this->belongsTo(Building::class);
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