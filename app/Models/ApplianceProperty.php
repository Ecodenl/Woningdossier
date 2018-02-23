<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplianceProperty extends Model
{
    public function appliance(){
    	return $this->belongsTo(Appliance::class);
    }
}
