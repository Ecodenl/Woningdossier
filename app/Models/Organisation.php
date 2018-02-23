<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Organisation extends Model
{
    public function user(){
    	return $this->belongsTo(User::class);
    }

    public function organisationType(){
    	return $this->belongsTo(OrganisationType::class);
    }

    public function industry(){
    	return $this->belongsTo(Industry::class);
    }
}