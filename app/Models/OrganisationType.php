<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrganisationType extends Model
{

	public $fillable = ['name', ];

	public function organisations(){
		return $this->hasMany(Organisation::class);
	}
}
