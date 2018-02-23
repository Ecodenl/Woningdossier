<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    public function opportunities(){
    	return $this->hasMany(Opportunity::class);
    }

}
