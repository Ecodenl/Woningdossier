<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Industry
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Organisation[] $organisations
 * @mixin \Eloquent
 * @property int $id
 * @property string $name
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Industry whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Industry whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Industry whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Industry whereUpdatedAt($value)
 */
class Industry extends Model
{
	public $fillable = ['name', ];

	public function organisations(){
		return $this->hasMany(Organisation::class);
	}
}
