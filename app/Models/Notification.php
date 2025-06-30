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
 * @property string $uuid
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\InputSource|null $inputSource
 * @method static Builder<static>|Notification allInputSources()
 * @method static Builder<static>|Notification forBuilding(\App\Models\Building|int $building)
 * @method static Builder<static>|Notification forInputSource(\App\Models\InputSource $inputSource)
 * @method static Builder<static>|Notification forMe(?\App\Models\User $user = null)
 * @method static Builder<static>|Notification forType(string $type)
 * @method static Builder<static>|Notification forUser(\App\Models\User|int $user)
 * @method static Builder<static>|Notification forUuid(string $uuid)
 * @method static Builder<static>|Notification newModelQuery()
 * @method static Builder<static>|Notification newQuery()
 * @method static Builder<static>|Notification query()
 * @method static Builder<static>|Notification residentInput()
 * @method static Builder<static>|Notification whereBuildingId($value)
 * @method static Builder<static>|Notification whereCreatedAt($value)
 * @method static Builder<static>|Notification whereId($value)
 * @method static Builder<static>|Notification whereInputSourceId($value)
 * @method static Builder<static>|Notification whereType($value)
 * @method static Builder<static>|Notification whereUpdatedAt($value)
 * @method static Builder<static>|Notification whereUuid($value)
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
    public function scopeForType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    public function scopeForUuid(Builder $query, string $uuid): Builder
    {
        return $query->where('uuid', $uuid);
    }
}
