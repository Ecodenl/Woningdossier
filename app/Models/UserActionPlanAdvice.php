<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserActionPlanAdvice extends Model
{

	public $fillable = [
		'user_id', 'measure_application_id', 'year',
	];

    public function user(){
    	return $this->belongsTo(User::class);
    }

    public function measureApplication(){
    	return $this->belongsTo(MeasureApplication::class);
    }

}
