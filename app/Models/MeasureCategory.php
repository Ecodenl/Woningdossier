<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeasureCategory extends Model
{
	public function categories(){
		return $this->belongsToMany(Measure::class);
	}

}
