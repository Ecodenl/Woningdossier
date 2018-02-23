<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserNote extends Model
{
    public function user(){
    	return $this->belongsTo(User::class);
    }

    public function createdBy(){
    	return $this->belongsTo(User::class, 'created_by_id');
    }

    public function updatedBy(){
    	return $this->belongsTo(User::class, 'updated_by_id');
    }



}
