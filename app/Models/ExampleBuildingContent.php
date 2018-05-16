<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExampleBuildingContent extends Model
{

	public $fillable = [
		'build_year', 'content',
	];

	/**
	 * The attributes that should be cast to native types.
	 *
	 * @var array
	 */
	protected $casts = [
		'content' => 'array',
	];

	public function exampleBuilding(){
		return $this->belongsTo(ExampleBuilding::class);
	}

	public function getValue($key){
		return array_get($this->content, $key);
	}

}
