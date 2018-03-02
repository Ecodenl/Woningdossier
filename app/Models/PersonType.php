<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PersonType
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\People[] $people
 * @mixin \Eloquent
 * @property int $id
 * @property string $name
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PersonType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PersonType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PersonType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PersonType whereUpdatedAt($value)
 */
class PersonType extends Model
{

	public $fillable = ['name', ];

	public function people(){
		return $this->hasMany(People::class);
	}
}
