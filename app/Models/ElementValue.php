<?php

namespace App\Models;

use App\Helpers\TranslatableTrait;
use Illuminate\Database\Eloquent\Model;

class ElementValue extends Model
{
    use TranslatableTrait;

    public function element(){
    	return $this->belongsTo(Element::class);
    }
}
