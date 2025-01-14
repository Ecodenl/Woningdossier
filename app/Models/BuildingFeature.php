<?php

namespace App\Models;

use App\Traits\GetMyValuesTrait;
use App\Traits\GetValueTrait;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * App\Models\BuildingFeature
 *
 * @property int $id
 * @property int|null $building_id
 * @property int|null $input_source_id
 * @property int|null $example_building_id
 * @property int|null $building_category_id
 * @property int|null $building_type_category_id
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
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\Building|null $building
 * @property-read \App\Models\BuildingCategory|null $buildingCategory
 * @property-read \App\Models\BuildingType|null $buildingType
 * @property-read \App\Models\FacadeSurface|null $contaminatedWallJoints
 * @property-read \App\Models\FacadeDamagedPaintwork|null $damagedPaintwork
 * @property-read \App\Models\EnergyLabel|null $energyLabel
 * @property-read \App\Models\ExampleBuilding|null $exampleBuilding
 * @property-read \App\Models\InputSource|null $inputSource
 * @property-read \App\Models\FacadePlasteredSurface|null $plasteredSurface
 * @property-read \App\Models\RoofType|null $roofType
 * @property-read \App\Models\FacadeSurface|null $wallJoints
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingFeature allInputSources()
 * @method static \Database\Factories\BuildingFeatureFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingFeature forBuilding(\App\Models\Building|int $building)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingFeature forInputSource(\App\Models\InputSource $inputSource)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingFeature forMe(?\App\Models\User $user = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingFeature forUser(\App\Models\User|int $user)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingFeature newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingFeature newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingFeature query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingFeature residentInput()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingFeature whereBuildYear($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingFeature whereBuildingCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingFeature whereBuildingHeatingApplicationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingFeature whereBuildingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingFeature whereBuildingLayers($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingFeature whereBuildingTypeCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingFeature whereBuildingTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingFeature whereCavityWall($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingFeature whereContaminatedWallJoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingFeature whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingFeature whereElementValues($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingFeature whereEnergyLabelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingFeature whereExampleBuildingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingFeature whereFacadeDamagedPaintworkId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingFeature whereFacadePlasteredPainted($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingFeature whereFacadePlasteredSurfaceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingFeature whereFloorSurface($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingFeature whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingFeature whereInputSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingFeature whereInsulationSurface($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingFeature whereInsulationWallSurface($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingFeature whereMonument($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingFeature whereRoofTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingFeature whereSurface($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingFeature whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingFeature whereVolume($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingFeature whereWallJoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingFeature whereWallSurface($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingFeature whereWindowSurface($value)
 * @mixin \Eloquent
 */
class BuildingFeature extends Model implements Auditable
{
    use GetValueTrait,
        GetMyValuesTrait,
        \App\Traits\Models\Auditable,
        HasFactory;

    protected $fillable = [
        'example_building_id',
        'element_values',
        'plastered_wall_surface',
        'building_type_category_id',
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

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    public function damagedPaintwork(): BelongsTo
    {
        return $this->belongsTo(FacadeDamagedPaintwork::class, 'facade_damaged_paintwork_id', 'id');
    }

    public function plasteredSurface(): BelongsTo
    {
        return $this->belongsTo(FacadePlasteredSurface::class, 'facade_plastered_surface_id', 'id');
    }

    public function wallJoints(): BelongsTo
    {
        return $this->belongsTo(FacadeSurface::class, 'wall_joints', 'id');
    }

    public function contaminatedWallJoints(): BelongsTo
    {
        return $this->belongsTo(FacadeSurface::class, 'contaminated_wall_joints', 'id');
    }

    public function buildingCategory(): BelongsTo
    {
        return $this->belongsTo(BuildingCategory::class);
    }

    public function exampleBuilding(): BelongsTo
    {
        return $this->belongsTo(ExampleBuilding::class);
    }

    public function buildingType(): BelongsTo
    {
        return $this->belongsTo(BuildingType::class);
    }

    public function roofType(): BelongsTo
    {
        return $this->belongsTo(RoofType::class);
    }

    public function energyLabel(): BelongsTo
    {
        return $this->belongsTo(EnergyLabel::class);
    }
}
