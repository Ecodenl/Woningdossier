<?php

namespace App\Helpers;

use App\Models\Translation;

trait TranslatableTrait {

	/**
	 * @param $key
	 *
	 * @return mixed|string
	 */
	public function getAttribute($key) {
		$attribute = parent::getAttribute($key);

		if ($this->isValidUuid($attribute)){
			return $this->translate($attribute, app()->getLocale());
		}
		return $attribute;
	}

	/**
	 * Check if a given string is a valid UUID
	 *
	 * @param   string  $uuid   The string to check
	 * @return  boolean
	 */
	protected function isValidUuid( $uuid ) {

		if (!is_string($uuid) || (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $uuid) !== 1)) {
			return false;
		}
		return true;
	}

	/**
	 * @param $attribute
	 * @param null $lang
	 *
	 * @return mixed|string
	 */
	public function translate($attribute, $lang = null){
		if (is_null($lang)){
			$lang = app()->getLocale();
		}
		$translation = Translation::where('key', $attribute)
			->where('language', $lang)
			->first();

		if (!$translation instanceof Translation){
			return $this->$attribute;
		}

		return $translation->translation;
	}


	/**
	 * Scope a query to check translations table.
	 *
	 * @param \Illuminate\Database\Eloquent\Builder $query
	 * @param string $locale
	 * @return \Illuminate\Database\Eloquent\Builder
	 */
	public function scopeTranslated($query, $attribute, $name , $locale = 'nl')
	{
		return $query->where('translations.language', '=', $locale)
		            ->where('translations.translation', '=', $name)
		            ->join('translations', $this->getTable() . '.'. $attribute, '=', 'translations.key');
	}
}