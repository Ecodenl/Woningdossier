<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\GetMyValuesTrait;
use App\Traits\GetValueTrait;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * App\Models\BuildingPvPanel
 *
 * @property int $id
 * @property int $building_id
 * @property int|null $input_source_id
 * @property int|null $total_installed_power
 * @property int|null $peak_power
 * @property int $number
 * @property int|null $pv_panel_orientation_id
 * @property int|null $angle
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\Building $building
 * @property-read \App\Models\InputSource|null $inputSource
 * @property-read \App\Models\PvPanelOrientation|null $orientation
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingPvPanel allInputSources()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingPvPanel forBuilding(\App\Models\Building|int $building)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingPvPanel forInputSource(\App\Models\InputSource $inputSource)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingPvPanel forMe(?\App\Models\User $user = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingPvPanel forUser(\App\Models\User|int $user)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingPvPanel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingPvPanel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingPvPanel query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingPvPanel residentInput()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingPvPanel whereAngle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingPvPanel whereBuildingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingPvPanel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingPvPanel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingPvPanel whereInputSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingPvPanel whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingPvPanel wherePeakPower($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingPvPanel wherePvPanelOrientationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingPvPanel whereTotalInstalledPower($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingPvPanel whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BuildingPvPanel extends Model implements Auditable
{
    use GetValueTrait,
        GetMyValuesTrait,
        \App\Traits\Models\Auditable;

    protected $fillable = [
        'building_id',
        'input_source_id',
        'peak_power',
        'number',
        'total_installed_power',
        'pv_panel_orientation_id',
        'angle',
        'comment',
    ];

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    public function orientation(): BelongsTo
    {
        return $this->belongsTo(PvPanelOrientation::class, 'pv_panel_orientation_id', 'id');
    }
}
