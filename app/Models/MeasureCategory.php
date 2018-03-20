<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\MeasureCategory
 *
 * @property int $id
 * @property string $name
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Measure[] $categories
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MeasureCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MeasureCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MeasureCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MeasureCategory whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class MeasureCategory extends Model
{
	public function categories(){
		return $this->belongsToMany(Measure::class);
	}

}
