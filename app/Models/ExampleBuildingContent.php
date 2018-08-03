<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ExampleBuildingContent
 *
 * @property int $id
 * @property int $example_building_id
 * @property int|null $build_year
 * @property array $content
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\ExampleBuilding $exampleBuilding
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExampleBuildingContent whereBuildYear($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExampleBuildingContent whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExampleBuildingContent whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExampleBuildingContent whereExampleBuildingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExampleBuildingContent whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExampleBuildingContent whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
