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
 * @method static Builder|Notification allInputSources()
 * @method static Builder|Notification forBuilding($building)
 * @method static Builder|Notification forInputSource(\App\Models\InputSource $inputSource)
 * @method static Builder|Notification forMe(?\App\Models\User $user = null)
 * @method static Builder|Notification forType(string $type)
 * @method static Builder|Notification forUser($user)
 * @method static Builder|Notification forUuid(string $uuid)
 * @method static Builder|Notification newModelQuery()
 * @method static Builder|Notification newQuery()
 * @method static Builder|Notification query()
 * @method static Builder|Notification residentInput()
 * @method static Builder|Notification whereBuildingId($value)
 * @method static Builder|Notification whereCreatedAt($value)
 * @method static Builder|Notification whereId($value)
 * @method static Builder|Notification whereInputSourceId($value)
 * @method static Builder|Notification whereType($value)
 * @method static Builder|Notification whereUpdatedAt($value)
 * @method static Builder|Notification whereUuid($value)
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
