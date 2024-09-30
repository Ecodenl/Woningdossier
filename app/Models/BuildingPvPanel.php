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
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingPvPanel allInputSources()
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingPvPanel forBuilding($building)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingPvPanel forInputSource(\App\Models\InputSource $inputSource)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingPvPanel forMe(?\App\Models\User $user = null)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingPvPanel forUser($user)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingPvPanel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingPvPanel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingPvPanel query()
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingPvPanel residentInput()
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingPvPanel whereAngle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingPvPanel whereBuildingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingPvPanel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingPvPanel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingPvPanel whereInputSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingPvPanel whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingPvPanel wherePeakPower($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingPvPanel wherePvPanelOrientationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingPvPanel whereTotalInstalledPower($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingPvPanel whereUpdatedAt($value)
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

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    /**
     * Make sure that null is casted to the integer 0.
     *
     * @param string $value
     *
     * @return void
     */
    public function setNumberAttribute(string $value): void
    {
        if (is_null($value)) {
            $value = 0;
        }

        $this->attributes['number'] = $value;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function orientation(): BelongsTo
    {
        return $this->belongsTo(PvPanelOrientation::class, 'pv_panel_orientation_id', 'id');
    }
}
