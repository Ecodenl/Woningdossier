<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Title extends Model
{

	public $fillable = ['name', ];

	public function users(){
		return $this->hasMany(User::class);
	}

	public function people(){
		return $this->hasMany(People::class);
	}
}
