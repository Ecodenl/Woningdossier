<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Cooperation.
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \App\Models\CooperationStyle $style
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cooperation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cooperation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cooperation whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cooperation whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cooperation whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Cooperation extends Model
{
    public $fillable = [
        'name',
        'slug'
    ];

    /**
     * The users associated with this cooperation.
     */
    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function style()
    {
        return $this->hasOne(CooperationStyle::class);
    }

	public function getRouteKeyName()
	{
		return 'slug';
	}

    /**
     * Return the coaches from the current cooperation
     *
     * @return $this
     */
    public function getCoaches()
    {
        $query = \DB::table('cooperations')
            ->select('users.*')
            ->where('cooperations.id', '=', $this->id)
            ->join('cooperation_user', 'cooperations.id', '=', 'cooperation_user.cooperation_id')
            ->join('model_has_roles', 'cooperation_user.user_id', '=', 'model_has_roles.model_id')
            ->where('model_has_roles.role_id', '=', 4)
            ->join('users', 'cooperation_user.user_id', '=', 'users.id');

        return $query;
	}

    /**
     * Return the residents from the current cooperation
     *
     * @return $this
     */
    public function getResidents()
    {
        $users = $this->users()->role('resident');

        return $users;

//        return $query = \DB::table('cooperations')
//        ->select('users.*')
//        ->where('cooperations.id', '=', $this->id)
//        ->leftJoin('cooperation_user', 'cooperations.id', '=', 'cooperation_user.cooperation_id')
//        ->leftJoin('model_has_roles', 'cooperation_user.user_id', '=', 'model_has_roles.model_id')
//        ->where('model_has_roles.role_id', '=', 5)
//        ->leftJoin('users', 'cooperation_user.user_id', '=', 'users.id');

	}

    public function getCoordinators()
    {
        $users = $this->users()->role('coordinator');

        return $users;
	}
}
