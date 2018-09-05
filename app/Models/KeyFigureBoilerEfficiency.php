<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\KeyFigureBoilerEfficiency
 *
 * @property int $id
 * @property int $service_value_id
 * @property int $heating
 * @property int $wtw
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\ServiceValue $serviceValue
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\KeyFigureBoilerEfficiency whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\KeyFigureBoilerEfficiency whereHeating($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\KeyFigureBoilerEfficiency whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\KeyFigureBoilerEfficiency whereServiceValueId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\KeyFigureBoilerEfficiency whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\KeyFigureBoilerEfficiency whereWtw($value)
 * @mixin \Eloquent
 */
class KeyFigureBoilerEfficiency extends Model
{

	public function serviceValue(){
		return $this->belongsTo(ServiceValue::class);
	}
}
