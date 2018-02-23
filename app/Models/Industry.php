<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Industry extends Model
{
	public function organisations(){
		return $this->hasMany(Organisation::class);
	}
}
