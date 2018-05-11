<?php

namespace App\Models;

use App\Helpers\TranslatableTrait;
use Illuminate\Database\Eloquent\Model;

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
		'content' => 'array',
		'is_default' => 'boolean',
	];

}
