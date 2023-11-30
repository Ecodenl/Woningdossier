<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Role
 *
 * @property int $id
 * @property string $name
 * @property string $human_readable_name
 * @property string $guard_name
 * @property int|null $input_source_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $level
 * @property-read \App\Models\InputSource|null $inputSource
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Permission\Models\Permission[] $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Account[] $users
 * @property-read int|null $users_count
 * @method static Builder|Role byName(string $name)
 * @method static Builder|Role newModelQuery()
 * @method static Builder|Role newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Role permission($permissions)
 * @method static Builder|Role query()
 * @method static Builder|Role whereCreatedAt($value)
 * @method static Builder|Role whereGuardName($value)
 * @method static Builder|Role whereHumanReadableName($value)
 * @method static Builder|Role whereId($value)
 * @method static Builder|Role whereInputSourceId($value)
 * @method static Builder|Role whereLevel($value)
 * @method static Builder|Role whereName($value)
 * @method static Builder|Role whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Role extends \Spatie\Permission\Models\Role
{
    // Scopes
    public function scopeByName(Builder $query, string $name): Builder
    {
        return $query->where('name', $name);
    }

    // Relations
    /**
     * Return the input source for the role.
     */
    public function inputSource(): BelongsTo
    {
        return $this->belongsTo(\App\Models\InputSource::class, 'input_source_id', 'id');
    }
}
