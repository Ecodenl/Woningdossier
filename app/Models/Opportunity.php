<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Opportunity extends Model
{
    public function measure(){
    	return $this->belongsTo(Measure::class);
    }

    public function user(){
    	return $this->belongsTo(User::class);
    }

    public function registration(){
    	return $this->belongsTo(Registration::class);
    }

    public function campaign(){
    	return $this->belongsTo(Campaign::class);
    }

    public function createdBy(){
    	return $this->belongsTo(User::class, 'created_by_id');
    }

    public function ownedBy(){
    	return $this->belongsTo(User::class, 'owned_by_id');
    }

	public function tasks(){
		return $this->hasMany(Task::class);
	}

}
