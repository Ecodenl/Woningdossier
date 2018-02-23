<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{

	public function type(){
		return $this->belongsTo(TaskType::class);
	}

	public function user(){
		return $this->belongsTo(User::class);
	}

	public function registration(){
		return $this->belongsTo(Registration::class);
	}

	public function opportunity(){
		return $this->belongsTo(Opportunity::class);
	}

	public function responsibleUser(){
		return $this->belongsTo(User::class, 'responsible_user_id');
	}

	public function finishedBy(){
		return $this->belongsTo(User::class, 'finished_by_id');
	}

	public function createdBy(){
		return $this->belongsTo(User::class, 'created_by_id');
	}

}
