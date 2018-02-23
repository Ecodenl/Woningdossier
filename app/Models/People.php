<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class People extends Model
{
    //
	public function user(){
		return $this->belongsTo(User::class);
	}

	public function organisation(){
		return $this->belongsTo(Organisation::class);
	}

	public function lastNamePrefix(){
		return $this->belongsTo(LastNamePrefix::class);
	}

	public function personType(){
		return $this->belongsTo(PersonType::class);
	}

	public function title(){
		return $this->belongsTo(Title::class);
	}
}