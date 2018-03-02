<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\MeasureCategory
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Measure[] $categories
 * @mixin \Eloquent
 * @property int $id
 * @property string $name
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MeasureCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MeasureCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MeasureCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MeasureCategory whereUpdatedAt($value)
 */
class MeasureCategory extends Model
{
	public function categories(){
		return $this->belongsToMany(Measure::class);
	}

}
