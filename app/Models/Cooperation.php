<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Cooperation
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\CooperationStyle $style
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cooperation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cooperation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cooperation whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cooperation whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cooperation whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Cooperation extends Model
{
    public $fillable = ['name', 'slug', ];

	/**
	 * The users associated with this cooperation
	 */
	public function users(){
		return $this->belongsToMany(User::class);
	}

	public function style(){
		return $this->hasOne(CooperationStyle::class);
	}

	public function getRouteKeyName() {
		return 'slug';
	}
}
