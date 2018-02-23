<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegistrationSource extends Model
{
	public function source(){
		return $this->belongsTo(Source::class);
	}

	public function registration(){
		return $this->belongsTo(Registration::class);
	}

}
