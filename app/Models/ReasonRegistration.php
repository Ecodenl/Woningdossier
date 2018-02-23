<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReasonRegistration extends Model
{
    //

	public function reason(){
		return $this->belongsTo(Reason::class);
	}

	public function registration(){
		return $this->belongsTo(Registration::class);
	}
}