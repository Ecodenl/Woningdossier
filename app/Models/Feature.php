<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Feature.
 *
 * @property int $id
 * @property string $name
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Feature whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Feature whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Feature whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Feature whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Feature extends Model
{
}
