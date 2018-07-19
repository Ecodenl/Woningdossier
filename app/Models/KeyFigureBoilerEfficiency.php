<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KeyFigureBoilerEfficiency extends Model
{

	public function serviceValue(){
		return $this->belongsTo(ServiceValue::class);
	}
}
