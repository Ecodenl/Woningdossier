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

	/**
	 * The attributes that should be cast to native types.
	 *
	 * @var array
	 */
	protected $casts = [
		'is_default' => 'boolean',
	];

}
