<?php

namespace App\Models;

use App\Helpers\TranslatableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * App\Models\ExampleBuilding
 *
 * @property int $id
 * @property string $translation_key
 * @property int|null $order
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExampleBuilding whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExampleBuilding whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExampleBuilding whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExampleBuilding whereTranslationKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExampleBuilding whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ExampleBuilding extends Model
{
    use TranslatableTrait;

	/**
	 * The attributes that should be cast to native types.
	 *
	 * @var array
	 */
	protected $casts = [
		'is_default' => 'boolean',
	];

	public $fillable = [
		'building_type_id', 'cooperation_id', 'order', 'is_default',
	];

	/**
	 * The "booting" method of the model.
	 *
	 * @return void
	 */
	protected static function boot() {
		parent::boot();

		self::deleting(function($model){
			/** @var ExampleBuilding $model */
			// delete contents
			$model->contents()->delete();
			// delete translations
			$translations = $model->getTranslations('name');
			if ($translations instanceof Collection){
				/** @var Translation $translation */
				foreach($translations as $translation){
					$translation->delete();
				}
			}
		});
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function contents(){
		return $this->hasMany(ExampleBuildingContent::class);
	}

	public function buildingType(){
		return $this->belongsTo(BuildingType::class);
	}

	public function cooperation(){
		return $this->belongsTo(Cooperation::class);
	}

	/**
	 * @param $year
	 *
	 * @return ExampleBuildingContent|null
	 */
	public function getContentForYear($year){
		return $this->contents()
		            ->where('build_year', '<=', $year)
		            ->orderBy('build_year')
		            ->take(1)
		            ->get();
	}

	/**
	 * @param int $year build year
	 * @param string $key step->slug . form_element_name (dot notation)
	 *
	 * @return mixed|null
	 */
	public function getExampleValueForYear($year, $key){
		$content = $this->getContentForYear($year);

		if (!$content instanceof ExampleBuildingContent){
			return null;
		}
		$content = $content->content;
		if (array_key_exists($key, $content)){
			return $content[$key];
		}
		return null;
	}

}
