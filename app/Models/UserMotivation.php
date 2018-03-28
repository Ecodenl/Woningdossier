<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserMotivation extends Model
{

    protected $fillable = ['user_id', 'motivation_id', 'order'];

	public function user(){
		return $this->belongsTo(User::class);
	}

	public function motivation(){
		return $this->belongsTo(Motivation::class);
	}
}
