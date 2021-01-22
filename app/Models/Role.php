<?php

namespace App\Models;

/**
 * App\Models\Role.
 *
 * @property int                                                                             $id
 * @property string                                                                          $name
 * @property string                                                                          $human_readable_name
 * @property string                                                                          $guard_name
 * @property int|null                                                                        $input_source_id
 * @property \Illuminate\Support\Carbon|null                                                 $created_at
 * @property \Illuminate\Support\Carbon|null                                                 $updated_at
 * @property int                                                                             $level
 * @property \App\Models\InputSource|null                                                    $inputSource
 * @property \Illuminate\Database\Eloquent\Collection|\Spatie\Permission\Models\Permission[] $permissions
 * @property int|null                                                                        $permissions_count
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\User[]                     $users
 * @property int|null                                                                        $users_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Role newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Role newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Role permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|Role query()
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereGuardName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereHumanReadableName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereInputSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Role extends \Spatie\Permission\Models\Role
{
    public static function boot()
    {
        parent::boot();

//        static::addGlobalScope(new );
    }

    /**
     * Return the input source for the role.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function inputSource()
    {
        return $this->belongsTo(\App\Models\InputSource::class, 'input_source_id', 'id');
    }
}
