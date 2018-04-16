<?php

namespace App\Models;

use App\Helpers\TranslatableTrait;
use Illuminate\Database\Eloquent\Model;

class ServiceValue extends Model
{
    use TranslatableTrait;

    public function keyFigureBoilerEfficiency(){
    	return $this->hasOne(KeyFigureBoilerEfficiency::class);
    }

}
