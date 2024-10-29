<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\GetMyValuesTrait;
use App\Traits\GetValueTrait;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BuildingPaintworkStatus
 *
 * @property int $id
 * @property int $building_id
 * @property int|null $input_source_id
 * @property int|null $last_painted_year
 * @property int|null $paintwork_status_id
 * @property int|null $wood_rot_status_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Building $building
 * @property-read \App\Models\InputSource|null $inputSource
 * @property-read \App\Models\PaintworkStatus|null $paintworkStatus
 * @property-read \App\Models\WoodRotStatus|null $woodRotStatus
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingPaintworkStatus allInputSources()
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingPaintworkStatus forBuilding($building)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingPaintworkStatus forInputSource(\App\Models\InputSource $inputSource)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingPaintworkStatus forMe(?\App\Models\User $user = null)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingPaintworkStatus forUser($user)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingPaintworkStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingPaintworkStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingPaintworkStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingPaintworkStatus residentInput()
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingPaintworkStatus whereBuildingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingPaintworkStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingPaintworkStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingPaintworkStatus whereInputSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingPaintworkStatus whereLastPaintedYear($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingPaintworkStatus wherePaintworkStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingPaintworkStatus whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingPaintworkStatus whereWoodRotStatusId($value)
 * @mixin \Eloquent
 */
class BuildingPaintworkStatus extends Model
{
    use GetValueTrait;
    use GetMyValuesTrait;
    

    protected $fillable = ['building_id', 'input_source_id', 'last_painted_year',  'paintwork_status_id', 'wood_rot_status_id'];

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    public function paintworkStatus(): BelongsTo
    {
        return $this->belongsTo(PaintworkStatus::class);
    }

    public function woodRotStatus(): BelongsTo
    {
        return $this->belongsTo(WoodRotStatus::class);
    }
}
