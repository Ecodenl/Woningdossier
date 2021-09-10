<?php

namespace App\Models;

use App\Traits\GetMyValuesTrait;
use App\Traits\GetValueTrait;
use App\Traits\ToolSettingTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BuildingFeature
 *
 * @property int $id
 * @property int|null $building_id
 * @property int|null $input_source_id
 * @property int|null $building_heating_application_id
 * @property int|null $building_category_id
 * @property int|null $building_type_id
 * @property int|null $roof_type_id
 * @property int|null $energy_label_id
 * @property int|null $cavity_wall
 * @property string|null $wall_surface
 * @property string|null $insulation_wall_surface
 * @property int|null $facade_plastered_painted
 * @property int|null $wall_joints
 * @property int|null $contaminated_wall_joints
 * @property int|null $element_values
 * @property int|null $facade_plastered_surface_id
 * @property int|null $facade_damaged_paintwork_id
 * @property string|null $surface
 * @property string|null $floor_surface
 * @property string|null $insulation_surface
 * @property string|null $window_surface
 * @property int|null $volume
 * @property int|null $build_year
 * @property int|null $building_layers
 * @property int|null $monument
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Building|null $building
 * @property-read \App\Models\BuildingCategory|null $buildingCategory
 * @property-read \App\Models\BuildingHeatingApplication|null $buildingHeatingApplication
 * @property-read \App\Models\BuildingType|null $buildingType
 * @property-read \App\Models\FacadeSurface|null $contaminatedWallJoints
 * @property-read \App\Models\FacadeDamagedPaintwork|null $damagedPaintwork
 * @property-read \App\Models\EnergyLabel|null $energyLabel
 * @property-read \App\Models\InputSource|null $inputSource
 * @property-read \App\Models\FacadePlasteredSurface|null $plasteredSurface
 * @property-read \App\Models\RoofType|null $roofType
 * @property-read \App\Models\FacadeSurface|null $wallJoints
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingFeature allInputSources()
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingFeature forBuilding(\App\Models\Building $building)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingFeature forInputSource(\App\Models\InputSource $inputSource)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingFeature forMe(?\App\Models\User $user = null)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingFeature newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingFeature newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingFeature query()
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingFeature residentInput()
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingFeature whereBuildYear($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingFeature whereBuildingCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingFeature whereBuildingHeatingApplicationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingFeature whereBuildingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingFeature whereBuildingLayers($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingFeature whereBuildingTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingFeature whereCavityWall($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingFeature whereContaminatedWallJoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingFeature whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingFeature whereElementValues($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingFeature whereEnergyLabelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingFeature whereFacadeDamagedPaintworkId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingFeature whereFacadePlasteredPainted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingFeature whereFacadePlasteredSurfaceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingFeature whereFloorSurface($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingFeature whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingFeature whereInputSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingFeature whereInsulationSurface($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingFeature whereInsulationWallSurface($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingFeature whereMonument($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingFeature whereRoofTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingFeature whereSurface($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingFeature whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingFeature whereVolume($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingFeature whereWallJoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingFeature whereWallSurface($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingFeature whereWindowSurface($value)
 * @mixin \Eloquent
 */
class BuildingFeature extends Model
{
    use GetValueTrait;
    use GetMyValuesTrait;
    use ToolSettingTrait;

    protected $fillable = [
        'element_values',
        'plastered_wall_surface',
        'building_type_id',
        'building_id',
        'wall_joints',
        'cavity_wall',
        'contaminated_wall_joints',
        'wall_surface',
        'insulation_wall_surface',
        'building_layers',
        'surface',
        'floor_surface',
        'monument',
        'insulation_surface',
        'build_year',
        'input_source_id',
        'facade_plastered_painted',
        'facade_plastered_surface_id',
        'facade_damaged_paintwork_id',
        'window_surface',
        'roof_type_id',
        'energy_label_id',
    ];

    public static function boot()
    {
        parent::boot();

        static::saving(function ($buildingFeature) {
            $primaryRoofType = $buildingFeature->roofType;

            if ($primaryRoofType instanceof RoofType) {
                $building = $buildingFeature->building;

                if ($building->roofTypes()->count === 0) {
                    // No roof types defined yet. Let's set them.
                    $roofTypeMap = [
                        'pitched' => 'pitched',
                        'flat' => 'flat',
                        'none' => 'none',
                        'gabled-roof' => 'pitched',
                        'rounded-roof' => 'pitched',
                        'straw-roof' => 'pitched',
                    ];

                    $roofTypeToLink = RoofType::findByShort($roofTypeMap[$primaryRoofType->short]);
                    $building->roofTypes()->associate($roofTypeToLink);
                }
            }
        });
    }

    public function building()
    {
        return $this->belongsTo(Building::class);
    }

    public function damagedPaintwork()
    {
        return $this->belongsTo(FacadeDamagedPaintwork::class, 'facade_damaged_paintwork_id', 'id');
    }

    public function plasteredSurface()
    {
        return $this->belongsTo(FacadePlasteredSurface::class, 'facade_plastered_surface_id', 'id');
    }

    /**
     * Column is named wall_joints.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function wallJoints()
    {
        return $this->belongsTo(FacadeSurface::class, 'wall_joints', 'id');
    }

    /**
     * Column is named contaminated_wall_joints.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function contaminatedWallJoints()
    {
        return $this->belongsTo(FacadeSurface::class, 'contaminated_wall_joints', 'id');
    }

    public function buildingCategory()
    {
        return $this->belongsTo(BuildingCategory::class);
    }

    public function buildingType()
    {
        return $this->belongsTo(BuildingType::class);
    }

    public function roofType()
    {
        return $this->belongsTo(RoofType::class);
    }

    public function energyLabel()
    {
        return $this->belongsTo(EnergyLabel::class);
    }
}
