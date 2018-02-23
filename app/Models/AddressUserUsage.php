<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AddressUserUsage extends Model
{
    public function address(){
    	return $this->belongsTo(Address::class);
    }

    public function user(){
    	return $this->belongsTo(User::class);
    }

}