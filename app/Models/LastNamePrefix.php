<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\LastNamePrefix
 *
 * @property int $id
 * @property string $name
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\People[] $people
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LastNamePrefix whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LastNamePrefix whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LastNamePrefix whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LastNamePrefix whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class LastNamePrefix extends Model
{

	public $fillable = [
		'name',
	];

	public function users(){
		return $this->hasMany(User::class);
	}

	public function people(){
		return $this->hasMany(People::class);
	}
}
