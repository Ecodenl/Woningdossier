<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Step
 *
 * @property int $id
 * @property string $slug
 * @property string $name
 * @property int $order
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Step whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Step whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Step whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Step whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Step whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Step whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Step extends Model
{
    //
}
