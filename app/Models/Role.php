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
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Account> $users
 * @property-read int|null $users_count
 * @method static Builder<static>|Role byName(string $name)
 * @method static Builder<static>|Role newModelQuery()
 * @method static Builder<static>|Role newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role permission($permissions, $without = false)
 * @method static Builder<static>|Role query()
 * @method static Builder<static>|Role whereCreatedAt($value)
 * @method static Builder<static>|Role whereGuardName($value)
 * @method static Builder<static>|Role whereHumanReadableName($value)
 * @method static Builder<static>|Role whereId($value)
 * @method static Builder<static>|Role whereInputSourceId($value)
 * @method static Builder<static>|Role whereLevel($value)
 * @method static Builder<static>|Role whereName($value)
 * @method static Builder<static>|Role whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role withoutPermission($permissions)
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
