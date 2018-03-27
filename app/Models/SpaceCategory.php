<?php

namespace App\Models;

use App\Helpers\TranslatableTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\SpaceCategory
 *
 * @property int $id
 * @property string $name
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SpaceCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SpaceCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SpaceCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SpaceCategory whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class SpaceCategory extends Model
{
    //
	use TranslatableTrait;
}
