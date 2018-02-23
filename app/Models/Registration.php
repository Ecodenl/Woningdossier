<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    //
	public function status(){
		return $this->belongsTo(RegistrationStatus::class);
	}

	public function opportunities(){
		return $this->hasMany(Opportunity::class);
	}

	public function tasks(){
		return $this->hasMany(Task::class);
	}
}
