<?php

namespace App\Models;

use App\Traits\GetMyValuesTrait;
use App\Traits\GetValueTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Notification
 *
 * @property int $id
 * @property int $building_id
 * @property int|null $input_source_id
 * @property string $type
 * @property bool $is_active
 * @property int $active_count
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\InputSource|null $inputSource
 * @method static Builder|Notification active()
 * @method static Builder|Notification activeNotifications(\App\Models\Building $building, \App\Models\InputSource $inputSource)
 * @method static Builder|Notification allInputSources()
 * @method static Builder|Notification forBuilding($building)
 * @method static Builder|Notification forInputSource(\App\Models\InputSource $inputSource)
 * @method static Builder|Notification forMe(?\App\Models\User $user = null)
 * @method static Builder|Notification forType(string $type)
 * @method static Builder|Notification forUser($user)
 * @method static Builder|Notification newModelQuery()
 * @method static Builder|Notification newQuery()
 * @method static Builder|Notification query()
 * @method static Builder|Notification residentInput()
 * @method static Builder|Notification whereActiveCount($value)
 * @method static Builder|Notification whereBuildingId($value)
 * @method static Builder|Notification whereCreatedAt($value)
 * @method static Builder|Notification whereId($value)
 * @method static Builder|Notification whereInputSourceId($value)
 * @method static Builder|Notification whereIsActive($value)
 * @method static Builder|Notification whereType($value)
 * @method static Builder|Notification whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Notification extends Model
{
    use GetMyValuesTrait,
        GetValueTrait;

    protected $fillable = [
        'type',
        'building_id',
        'input_source_id',
        'uuid',
    ];

    # Scopes
    public function scopeActiveNotifications(Builder $query, Building $building, InputSource $inputSource)
    {
        return $query
            ->forBuilding($building)
            ->forInputSource($inputSource);
    }

    public function scopeForType(Builder $query, string $type)
    {
        return $query->where('type', $type);
    }
}
