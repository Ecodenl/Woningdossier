<?php

namespace App\Models;

use App\Helpers\TranslatableTrait;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
	use TranslatableTrait;

	public function serviceType(){
		return $this->belongsTo(ServiceType::class);
	}

	public function values(){
		return $this->hasMany(ServiceValue::class);
	}

}
